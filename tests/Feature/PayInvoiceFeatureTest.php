<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PayInvoiceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_pay_invoice_with_valid_2sv_code(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 1000, 'is_active' => true]);
        $invoice = Invoice::factory()->create(['user_id' => $user->id, 'amount' => 200]);

        $code = '123456';
        $user->twoStepVerifications()->create([
            'invoice_id' => $invoice->id,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->actingAs($user)
            ->postJson("/api/invoice/{$invoice->id}/pay", ['code' => $code])
            ->assertOk()
            ->assertJson(['message' => 'Invoice paid successfully']);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'paid_at' => now()
        ]);
    }

    public function test_user_cannot_pay_invoice_with_invalid_code(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create(['user_id' => $user->id, 'balance' => 1000, 'is_active' => true]);
        $invoice = Invoice::factory()->create(['user_id' => $user->id, 'amount' => 200]);

        $user->twoStepVerifications()->create([
            'invoice_id' => $invoice->id,
            'code' => Hash::make('correct-code'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->actingAs($user)
            ->postJson("/api/invoice/{$invoice->id}/pay", ['code' => 'wrong-code'])
            ->assertStatus(422);

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
            'paid_at' => now()
        ]);
    }

    public function test_user_cannot_pay_invoice_without_code(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson("/api/invoice/{$invoice->id}/pay", [])
            ->assertStatus(422);
    }
}
