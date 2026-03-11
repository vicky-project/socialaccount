<?php
namespace Modules\SocialAccount\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Modules\SocialAccount\Services\SocialProviderManager;
use Modules\SocialAccount\Models\SocialAccount;
use Modules\Users\Models\User;

class SocialLoginController extends Controller
{
  protected $manager;

  public function __construct(SocialProviderManager $manager) {
    $this->manager = $manager;
  }

  public function redirectToProvider($provider) {
    $providerInstance = $this->manager->getProvider($provider);
    if (!$providerInstance) {
      abort(404, "Provider not found");
    }
    return Socialite::driver($provider)->redirect();
  }

  public function handleProviderCallback(Request $request, $provider) {
    $providerInstance = $this->manager->getProvider($provider);
    if (!$providerInstance) {
      abort(404, "Provider not found");
    }

    try {
      $socialUser = Socialite::driver($provider)->user();
    } catch (\Exception $e) {
      \Log::error("Failed to authenticate with {$provider}", [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return redirect()->route('login')->withErrors("Failed to authenticate with $provider.");
    }

    // Ambil data dari provider (termasuk providerable_id, type, dll)
    $providerData = $providerInstance->handleCallback($socialUser);

    // Cari social account berdasarkan provider + provider_id (yang ada di providerable)
    // Karena kita tidak menyimpan provider_id di social_accounts langsung, kita perlu mencari melalui relasi providerable.
    // Alternatif: kita bisa menyimpan provider_id di provider_data atau di kolom tersendiri?
    // Dalam rancangan ini, kita menggunakan morph, jadi provider_id tersimpan di tabel provider.
    // Untuk pencarian, kita harus join atau mencari dengan dua langkah.
    // Kita bisa mencari social account dengan provider = $provider dan providerable_id = $providerData['providerable_id']
    // Asumsikan setiap provider memiliki kolom 'provider_id' di tabelnya (sesuai standar).
    // Tapi untuk memudahkan, kita bisa tambahkan kolom 'provider_id' di social_accounts? Tidak, karena sudah ada di tabel provider.
    // Maka kita perlu mencari dengan cara:
    // $socialAccount = SocialAccount::where('provider', $provider)
    //     ->whereHasMorph('providerable', [$providerData['providerable_type']], function ($q) use ($socialUser) {
    //         $q->where('provider_id', $socialUser->getId());
    //     })->first();

    $providerModel = $providerData['providerable_type'];
    $socialAccount = SocialAccount::where('provider', $provider)
    ->where('providerable_id', $providerData['providerable_id'])
    ->where('providerable_type', $providerModel)
    ->first();

    // Jika user sudah login (menghubungkan akun)
    if (Auth::check()) {
      $user = Auth::user();
      if ($socialAccount && $socialAccount->user_id != $user->id) {
        return redirect()->route('profile')->withErrors('Akun ini sudah terhubung dengan pengguna lain.');
      }

      if (!$socialAccount) {
        // Buat social account baru
        $authlogId = $this->getCurrentAuthLogId($user);
        SocialAccount::create(array_merge([
          'user_id' => $user->id,
          'authlog_id' => $authlogId,
          'provider' => $provider,
          'last_used_at' => now(),
        ], $providerData));
      } else {
        // Update last_used_at
        $socialAccount->update(['last_used_at' => now()]);
      }
      return redirect()->route('profile')->with('success', 'Akun berhasil dihubungkan.');
    }

    // Proses login
    if ($socialAccount) {
      $user = $socialAccount->user;
      Auth::login($user);
      // Update last_used_at
      $socialAccount->update(['last_used_at' => now()]);
      return redirect()->intended(config("socialaccount.url_after_login"));
    }

    // Cari user berdasarkan email (jika ada)
    $user = null;
    if ($socialUser->getEmail()) {
      $user = User::where('email', $socialUser->getEmail())->first();
    }

    if (!$user) {
      return redirect()->route("login")->withErrors("Tidak ditemukan user dengan provider: {$provider}. Silakan login manual atau registrasi user baru.");
    }

    $authlogId = $this->getCurrentAuthLogId($user);
    if (!$authlogId) {
      return redirect()->route("login")->withErrors("Anda belum pernah login sebelumnya. Silakan login terlebih dahulu menggunakan credential.");
    }

    SocialAccount::create(array_merge([
      'user_id' => $user->id,
      'authlog_id' => $authlogId,
      'provider' => $provider,
      'last_used_at' => now(),
    ], $providerData));

    Auth::login($user);
    return redirect()->intended(config("socialaccount.url_after_login"));
  }

  /**
  * Mendapatkan ID log autentikasi terbaru untuk user (jika package terpasang)
  */
  protected function getCurrentAuthLogId($user) {
    $authLogTable = config('socialaccount.authentication_log_table', 'authentication_log');
    if (!Schema::hasTable($authLogTable)) {
      return null;
    }

    // Ambil log terbaru untuk user ini (misalnya login terakhir)
    $log = $user->authentications()->first();

    return $log ? $log->id : null;
  }
}