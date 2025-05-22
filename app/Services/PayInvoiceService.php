<?php

namespace App\Services;

use App\Exceptions\PaymentException;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayInvoiceService
{
    public function __construct(
        private InvoiceService $invoiceService,
        private WalletService  $walletService,
    )
    {
    }

    public function handle(User $user, Invoice $invoice): void
    {
        try {
            $this->invoiceService->ensureOwnership($invoice, $user->id);
            $this->invoiceService->ensureInvoiceIsValid($invoice);
            $this->walletService->ensureWalletIsUsable($user->wallet);

            DB::transaction(function () use ($user, $invoice) {
                $this->walletService->deductBalance($user->wallet, $invoice->amount);
                $this->invoiceService->markAsPaid($invoice);
            });
        } catch (PaymentException $e) {
            dd($e);
        }
    }
}
