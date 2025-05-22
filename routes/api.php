<?php

use App\Http\Controllers\InvoicePaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/invoices/{invoice}/pay', InvoicePaymentController::class)
        ->name('invoices.pay');
});
