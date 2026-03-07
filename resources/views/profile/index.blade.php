<div class="card mt-3">
  <div class="card-header">
    <i class="bi bi-shield-lock"></i> Akun Sosial
  </div>
  <div class="card-body">
    @if(session('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
    </div>
    @endif

    <p>
      Hubungkan akun sosial media Anda untuk memudahkan login.
    </p>

    @if($connectedAccounts->count() > 0)
    <ul class="list-group mb-3">
      @foreach($connectedAccounts as $account)
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>
          <i class="bi bi-{{ $account->provider->value }}"></i> {{ ucfirst($account->provider->value) }}
          @if($account->providerable)
          <small class="text-muted">
            ({{ $account->providerable->email ?? $account->providerable->username ?? $account->providerable->provider_id }})
          </small>
          @endif
        </span>
        <form action="{{ route('profile.social.disconnect', $account->id) }}" method="POST">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-danger" onclick="return confirm('Putuskan akun ini?')">Putuskan</button>
        </form>
      </li>
      @endforeach
    </ul>
    @else
    <p class="text-muted">
      Belum ada akun sosial yang terhubung.
    </p>
    @endif

    <h6>Hubungkan akun baru:</h6>
    <div class="d-flex flex-wrap gap-2">
      @php
      // Kumpulkan nama provider yang sudah terhubung
      $connectedProviderNames = $connectedAccounts->pluck('provider')->map(fn($provider) =>$provider->value)->toArray();
      // Filter provider yang belum terhubung
      $availableProviders = collect($providers)->filter(function($provider) use ($connectedProviderNames) {
      return !in_array($provider->getName(), $connectedProviderNames);
      });
      dd($availableProviders, $connectedProviderNames);
      @endphp

      @forelse($availableProviders as $provider)
      <a href="{{ route('profile.social.connect', $provider->getName()) }}" class="btn btn-outline-secondary">
        <i class="{{ $provider->getIcon() }}"></i> {{ $provider->getLabel() }}
      </a>
      @empty
      <p class="text-muted">
        Semua akun sosial sudah terhubung.
      </p>
      @endforelse
    </div>
  </div>
</div>