<?php
namespace Modules\SocialAccount\Services;

use Modules\SocialAccount\Interfaces\SocialProvider;

class SocialProviderManager
{
  protected array $providers = [];

  public function register(SocialProvider $provider): void
  {
    $this->providers[$provider->getName()] = $provider;
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