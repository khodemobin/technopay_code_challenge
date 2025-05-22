<?php

use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\TwoStepVerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/invoice/{invoice}/pay', InvoicePaymentController::class);

    Route::prefix('/invoice/{invoice}/2sv')->group(function () {
        Route::post('/initiate', [TwoStepVerificationController::class, 'initiate']);
        Route::post('/verify', [TwoStepVerificationController::class, 'verify']);
    });
});
