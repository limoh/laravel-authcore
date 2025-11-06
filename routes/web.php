<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ConsentController;

Route::middleware('auth:web')->group(function () {
    Route::get('/oauth/consent', [ConsentController::class, 'show'])->name('auth.consent.show');
    Route::post('/oauth/consent/approve', [ConsentController::class, 'approve'])->name('auth.consent.approve');
    Route::post('/oauth/consent/deny', [ConsentController::class, 'deny'])->name('auth.consent.deny');
});

Route::get('/authorize', [\App\Http\Controllers\API\OAuthController::class, 'showAuthorizeForm'])->name('passport.authorize');
Route::post('/authorize', [\App\Http\Controllers\API\OAuthController::class, 'approve']);