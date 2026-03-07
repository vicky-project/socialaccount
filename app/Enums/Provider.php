<?php
namespace Modules\Users\Enums;

enum Provider: string
{
  case TELEGRAM = "telegram";

    public function label(): string
    {
      return match ($this) {
        self::TELEGRAM => "Telegram",
      };
    }
}