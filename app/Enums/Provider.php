<?php
namespace Modules\SocialAccount\Enums;

enum Provider: string
{
  case GOOGLE = "google";
  case TELEGRAM = "telegram";

    public function label(): string
    {
      return match ($this) {
        self::GOOGLE => "Google",
        self::TELEGRAM => "Telegram",
      };
    }
}