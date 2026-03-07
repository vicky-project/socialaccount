<?php

use Illuminate\Support\Facades\Route;
use Modules\SocialAccount\Http\Controllers\SocialLoginController;

Route::group(['middleware' => 'web'], function() {
  Route::get('auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.login');
  Route::get('auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback'])->name('social.callback');
});

Route::group(['middleware' => 'auth'], function() {});