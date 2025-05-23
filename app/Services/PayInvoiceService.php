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
        private NotificationService        $notificationService
    )
    {
    }

    /**
     * @throws PaymentException
     */
    public function handle(User $user, Invoice $invoice, string $code): void
    {
        $lockKey = 'pay_invoice_user_' . $user->id;
        $lock = cache()->lock($lockKey, 10);

        if (!$lock->get()) {
            abort(429,'Another payment process is already running. Please try again shortly.');
        }


        try {
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

            $this->notificationService->sendSuccess($user, $invoice);
        } catch (PaymentException $e) {
            $this->walletService->refund($user->wallet, $invoice->amount ?? 0);
            $this->notificationService->sendFailure($user, $invoice, $e->getMessage());
            throw $e;
        } finally {
            $lock->release();
        }
    }
}
