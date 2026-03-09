<?php
namespace Modules\SocialAccount\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SocialAccount\Models\SocialAccount;
use Modules\SocialAccount\Services\SocialProviderManager;

class ProfileController extends Controller
{
  public function index(SocialProviderManager $manager) {
    $user = auth()->user();
    $connectedAccounts = $user->socialAccounts()->with('providerable')->get();
    $providers = $manager->getProviders();

    return view('socialaccount::profile.index', compact('connectedAccounts', 'providers'));
  }

  public function disconnect($id) {
    $account = SocialAccount::where('user_id', auth()->id())->findOrFail($id);
    // Hapus juga providerable jika perlu? Atau biarkan saja karena mungkin ada relasi lain.
    // Tergantung kebijakan. Bisa hapus providerable juga.
    $account->providerable()->delete();
    $account->delete();
    return redirect()->back()->with('success', 'Akun berhasil diputuskan.');
  }
}