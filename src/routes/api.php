<?php


use Illuminate\Support\Facades\Route;
use LP\TwoFA\Http\Controllers\TwoFA\AuthQrCodeController;
use LP\TwoFA\Http\Controllers\TwoFA\AuthTwoFAController;
use LP\TwoFA\Http\Controllers\TwoFA\RecoveryCodeController;

Route::middleware('auth:api')->group(function () {

    Route::apiResource('two-factor-qr-code', AuthQrCodeController::class)
        ->only(
            [
                'index',
            ]
        );
    Route::apiResource('two-factor-authentication', AuthTwoFAController::class)
        ->only(
            [
                'store',
                'destroy',
            ]
        )->parameters([
            'two-factor-authentication' => 'user',
        ]);

    Route::apiResource('two-factor-recovery-codes', RecoveryCodeController::class)
        ->only(
            [
                'index',
            ]
        );
});
