<?php

namespace App\Services;

use App\Exceptions\PaymentException;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

readonly class PayInvoiceService
{
    public function __construct(
        private InvoiceService             $invoiceService,
        private WalletService              $walletService,
        private DailySpendingLimitService  $limitService,
        private TwoStepVerificationService $twoStepService,
    )
    {
    }

    /**
     * @throws PaymentException
     */
    public function handle(User $user, Invoice $invoice, string $code): void
    {

        $this->invoiceService->ensureOwnership($invoice, $user->id);
        $this->invoiceService->ensureInvoiceIsValid($invoice);
        $this->walletService->ensureWalletIsUsable($user->wallet);

        $this->twoStepService->verify($user, $invoice, $code);

        $this->limitService->checkAndApplyLimit($invoice->amount, function () use ($user, $invoice) {
            DB::transaction(function () use ($user, $invoice) {
                $this->walletService->deductBalance($user->wallet, $invoice->amount);
                $this->invoiceService->markAsPaid($invoice);
            });
        });

    }
}
