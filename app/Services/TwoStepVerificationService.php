<?php

namespace App\Services;

use App\Exceptions\PaymentException;
use App\Models\Invoice;
use App\Models\PaymentConfirmation;
use App\Models\User;
use App\Notifications\TwoStepCodeNotification;
use Illuminate\Support\Facades\Hash;

class TwoStepVerificationService
{
    public function initiate(User $user, Invoice $invoice): void
    {
        PaymentConfirmation::query()->where('user_id', $user->id)
            ->where('invoice_id', $invoice->id)
            ->delete();

        $plainCode = random_int(100000, 999999);

        PaymentConfirmation::query()->create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'code' => Hash::make($plainCode),
            'expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new TwoStepCodeNotification($plainCode));
    }

    /**
     * @throws PaymentException
     */
    public function verify(User $user, Invoice $invoice, string $inputCode): void
    {
        $confirmation = PaymentConfirmation::query()->where('user_id', $user->id)
            ->where('invoice_id', $invoice->id)
            ->where('confirmed', false)
            ->latest()
            ->first();

        if (!$confirmation || $confirmation->isExpired()) {
            abort(422, 'Invalid or expired confirmation code.');
        }

        if (!Hash::check($inputCode, $confirmation->code)) {
            abort(422, 'Invalid or expired confirmation code.');
        }

        $confirmation->update(['confirmed' => true]);
    }
}
