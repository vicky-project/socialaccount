<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		Schema::create("social_accounts", function (Blueprint $table) {
			$table->id();
			$table
				->foreignId("user_id")
				->constrained()
				->onDelete("cascade");
			if (Schema::hasTable(config("authentication-log.table_name"))) {
				$table
					->foreignId("authlog_id")
					->constrained(config("authentication-log.table_name"))
					->onDelete("cascade");
			}

			$table->string("provider"); // google, facebook, github, etc
			$table->morphs("providerable"); // morph to provider table
			$table->timestamp("last_used_at")->useCurrent();
			$table->json("provider_data")->nullable();
			$table->timestamps();

			$indexes = ["user_id", "providerable_id"];

			if (Schema::hasTable(config("authentication-log.table_name"))) {
				$indexes[] = "authlog_id";
			}

			$table->index($indexes);
		});
	}

	public function down()
	{
		Schema::dropIfExists("social_accounts");
	}
};
