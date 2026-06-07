<?php
namespace Modules\SocialAccount\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Modules\SocialAccount\Enums\Provider;
use Modules\SocialAccount\Models\SocialAccount;
use Modules\SocialAccount\Services\SocialProviderManager;

class ProfileController extends Controller
{
  public function index(Request $request, SocialProviderManager $manager) {
    $user = $request->user();
    $connectedAccounts = $user->socialAccounts()->with('providerable')->get();
    $providers = $manager->getProviders();

    return view('socialaccount::profile.index', compact('connectedAccounts', 'providers'));
  }

  public function open(Request $request, Provider $provider) {
    $user = $request->user();
    $account = SocialAccount::where('user_id', $user->id)->where('provider', $provider)->first();
    $link = $account->providerable->openLink();
    if (!$link || $link == '') {
      return back()->with('error', 'No url provider to open.');
    }

    $params = null;
    if (str($link)->contains('@')) {
      $parts = str($link)->explode('@')->toArray();
      $link = $parts[0];
      $params = $parts[1];
    }

    if (!Route::has($link)) {
      return back()->with('error', 'Route not defined.');
    }

    if ($params) {
      return to_route($link, $params);
    }
    return to_route($link);
  }

  public function disconnect(Request $request, $id) {
    $user = $request->user();
    $account = SocialAccount::where('user_id', $user->id)->findOrFail($id);
    // Hapus juga providerable jika perlu? Atau biarkan saja karena mungkin ada relasi lain.
    // Tergantung kebijakan. Bisa hapus providerable juga.
    //$account->providerable()->delete();
    $account->delete();
    return redirect()->back()->with('success', 'Akun berhasil diputuskan.');
  }
}