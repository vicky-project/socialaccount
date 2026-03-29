<?php
namespace Modules\SocialAccount\Services;

use Modules\Users\Models\User;
use Modules\SocialAccount\Enums\Provider;
use Modules\SocialAccount\Interfaces\SocialAccountInterface;
use Modules\SocialAccount\Models\SocialAccount;

class SocialAccountService
{
  public function getByUserId(int $userId) {
    return SocialAccount::whereHas("user", function ($query) use ($userId) {
      $query->where("id", $userId);
    })->get();
  }

  public function getByAuthlogId($authId) {
    return SocialAccount::where("authlog_id", $authId)->get();
  }

  public function saveUserSocialAccountByProvider(
    User $user,
    SocialAccountInterface $model,
    string|Provider $provider,
    array $options = []
  ) {
    $authenticationLog = $user->latestAuthentication;

    if ($authenticationLog) {
      return $model->provider()->firstOrCreate(
        [
          "user_id" => $user->id,
          "provider" => $provider
        ],
        [
          "authlog_id" => $authenticationLog->id,
          "provider_data" => $options ?? [],
        ]
      );
    }

    return $authenticationLog;
  }
}