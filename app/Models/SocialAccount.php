<?php

namespace Modules\SocialAccount\Models;

use Modules\SocialAccount\Enums\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SocialAccount extends Model
{
  use LogsActivity;

  protected $table = "social_accounts";

  protected $with = ["providerable"];

  protected $fillable = [
    "user_id",
    "authlog_id",
    "provider",
    "providerable_id",
    "providerable_type",
    "last_used_at",
    "provider_data",
  ];

  protected $casts = [
    "provider" => Provider::class,
    "last_used_at" => "datetime",
    "provider_data" => "array",
  ];

  public static function booted() {
    static::deleting(function ($socialAccount) {
      if ($socialAccount->providerable) {
        $socialAccount->providerable->delete();
      }
    });
  }

  /**
  * Activity Log Options
  */
  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
    ->logOnly(["provider",
      "providerable_id"])
    ->logOnlyDirty()
    ->setDescriptionForEvent(
      fn(string $eventName) => "Social account {$eventName}"
    )
    ->useLogName("social-accounts");
  }

  /**
  * Many-to-One: SocialAccount belongs to User
  */
  public function user() {
    return $this->belongsTo(config('auth.providers.users.model'));
  }

  public function providerable(): MorphTo
  {
    return $this->morphTo();
  }

  /**
  * Scope Methods
  */

  public function scopeByProvider($query,
    $provider) {
    return $query->where("provider",
      $provider);
  }

  public function getProviderDataAttribute($value) {
    return json_decode($value,
      true) ?? [];
  }

  public function getUser() {
    return $this->user()->first();
  }
}