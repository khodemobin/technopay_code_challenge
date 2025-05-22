<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfirmation extends Model
{
    protected $guarded = ['id'];

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function markAsConfirmed(): void
    {
        $this->update(['confirmed' => true]);
    }
}
