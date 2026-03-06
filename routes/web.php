<?php

use Illuminate\Support\Facades\Route;
use Modules\SocialAccount\Http\Controllers\SocialAccountController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('socialaccounts', SocialAccountController::class)->names('socialaccount');
});
