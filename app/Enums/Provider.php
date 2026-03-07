<?php
namespace Modules\SocialAccount\Enums;

enum Provider: string
{
  case GOOGLE = "google";
  case GITHUB = "github";
  case TELEGRAM = "telegram";

    public function label(): string
    {
      return match ($this) {
        self::GOOGLE => "Google",
        self::GITHUB => "Github",
        self::TELEGRAM => "Telegram",
      };
    }
}