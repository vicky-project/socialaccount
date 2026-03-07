<?php

use Illuminate\Support\Facades\Route;
use Modules\SocialAccount\Http\Controllers\SocialLoginController;
use Modules\SocialAccount\Http\Controllers\ProfileController;

Route::group(['middleware' => 'web'], function() {
  Route::get('auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.login');
  Route::get('auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback'])->name('social.callback');
});

Route::group(['middleware' => 'auth'], function() {
  Route::get('/profile/social', [ProfileController::class, 'index'])->name('profile.social');
  Route::get('/profile/social/{provider}', [ProfileController::class, 'connect'])->name('profile.social.connect');
  Route::get('/profile/social/{id}', [ProfileController::class, 'disconnect'])->name('profile.social.disconnect');
});