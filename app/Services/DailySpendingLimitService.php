<?php

namespace App\Services;

use App\Exceptions\PaymentException;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class DailySpendingLimitService
{
    public function checkAndApplyLimit(float $amount, callable $onLimitOk): void
    {
        DB::transaction(static function () use ($amount, $onLimitOk) {
            $todayTotal = Invoice::paidToday()->sum('amount');

            if ($todayTotal + $amount > config('wallet.daily_limit')) {
                throw new PaymentException("Daily spending limit reached.");
            }

            $onLimitOk();
        });
    }
}
