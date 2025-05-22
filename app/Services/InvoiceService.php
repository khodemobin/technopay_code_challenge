<?php

namespace App\Services;

use App\Exceptions\PaymentException;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * @throws PaymentException
     */
    public function ensureInvoiceIsValid(Invoice $invoice): void
    {
        if ($invoice->isExpired()) {
            throw new PaymentException('Invoice has expired.');
        }

        if ($invoice->isPaid()) {
            throw new PaymentException('Invoice is already paid.');
        }
    }

    public function ensureOwnership(Invoice $invoice, int $userId): void
    {
        if ($invoice->user_id !== $userId) {
            abort(403, 'You do not have permission to pay this invoice.');
        }
    }

    public function markAsPaid(Invoice $invoice): void
    {
        $invoice->paid_at = Carbon::now();
        $invoice->save();
    }
}
