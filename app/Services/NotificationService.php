<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;

class NotificationService
{
    public function sendSuccess(User $user, Invoice $invoice): void
    {
        //TODO: We can add some logics for sending notification in real-world project
        //Notification::send($user, new TransactionSuccess($message));

        info("User {$user->id} notified: Invoice {$invoice->id} paid successfully.");
    }

    public function sendFailure(User $user, Invoice $invoice, string $message): void
    {
        //TODO: We can add some logics for sending notification in real-world project
        //Notification::send($user, new TransactionSuccess($message));

        info("User {$user->id} notified: Payment for Invoice {$invoice->id} failed. Reason: {$message}");
    }
}
