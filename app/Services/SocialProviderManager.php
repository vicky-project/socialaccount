<?php
namespace Modules\SocialAccount\Services;

use Illuminate\Support\Facades\Log;
use Modules\SocialAccount\Interfaces\SocialProvider;

class SocialProviderManager
{
  protected array $providers = [];

  public function register(SocialProvider $provider): void
  {
    $this->providers[$provider->getName()] = $provider;
    Log::info("Provider registered: {$provider->getName()}");
  }

  public function getProviders(): array
  {
    return $this->providers;
  }

  public function getProvider(string $name): ?SocialProvider
  {
    return $this->providers[$name] ?? null;
  }
}