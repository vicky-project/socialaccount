<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up() {
    $authLogTable = config('socialaccount.authentication_log_table', config('authentication-log.table_name'));

    Schema::create("social_accounts", function (Blueprint $table) use($authLogTable) {
      $table->id();
      $table
      ->foreignId("user_id")
      ->constrained()
      ->onDelete("cascade");
      if (Schema::hasTable($authLogTable)) {
        $table
        ->foreignId("authlog_id")
        ->constrained(config("authentication-log.table_name"))
        ->nullable()
        ->onDelete("cascade");
      } else {
        $table->unsignedBigInteger('authlog_id')->nullable();
      }

      $table->string("provider", 50); // google, facebook, github, etc
      $table->morphs("providerable"); // morph to provider table
      $table->timestamp("last_used_at")->nullable();
      $table->json("provider_data")->nullable();
      $table->timestamps();

      $table->index(["user_id",
        "provider"]);
      $table->index('providerable_id');

      if (Schema::hasTable($authLogTable)) {
        $table->index("authlog_id");
      }
    });
  }

  public function down() {
    Schema::dropIfExists("social_accounts");
  }
};