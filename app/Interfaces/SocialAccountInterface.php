<?php
namespace Modules\SocialAccount\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface SocialAccountInterface
{
  public function provider(): MorphOne;
}