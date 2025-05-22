<?php

namespace App\Services;

use App\Exceptions\PaymentException;
use App\Models\Wallet;

class WalletService
{
    /**
     * @throws PaymentException
     */
    public function ensureWalletIsUsable(Wallet $wallet): void
    {
        if (! $wallet->is_active) {
            throw new PaymentException('Wallet is not active.');
        }
    }

    /**
     * @throws PaymentException
     */
    public function deductBalance(Wallet $wallet, float $amount): void
    {
        if ($wallet->balance < $amount) {
            throw new PaymentException('Insufficient balance.');
        }

        $wallet->decrement('balance', $amount);
    }

    public function refund(Wallet $wallet, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $wallet->increment('balance', $amount);
    }
}
