<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayInvoiceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_pay_invoice_successfully(): void
    {
        $user = User::factory()
            ->has(Wallet::factory(['balance' => 500]))
            ->has(Invoice::factory(['amount' => 100]))
            ->create();

        $invoice = $user->invoices->first();

        $this->actingAs($user)
            ->postJson(route('invoices.pay', $invoice))
            ->assertOk()
            ->assertJsonStructure(['message', 'invoice_id', 'paid_at']);

        $this->assertNotNull($invoice->fresh()->paid_at);
        $this->assertEquals(400, $user->wallet->fresh()->balance);
    }

    public function test_cannot_pay_invoice_with_insufficient_balance(): void
    {
        $user = User::factory()
            ->has(Wallet::factory(['balance' => 10]))
            ->has(Invoice::factory(['amount' => 100]))
            ->create();

        $invoice = $user->invoices->first();

        $this->actingAs($user)
            ->postJson(route('invoices.pay', $invoice))
            ->assertStatus(500)
            ->assertJson(['message' => 'Insufficient balance.']);

        $this->assertNull($invoice->fresh()->paid_at);
        $this->assertEquals(10, $user->wallet->fresh()->balance);
    }

    public function test_cannot_pay_invoice_if_not_owner(): void
    {
        $owner = User::factory()->create();
        $invoice = Invoice::factory(['user_id' => $owner->id, 'amount' => 100])->create();
        Wallet::factory(['user_id' => $owner->id, 'balance' => 200])->create();

        $attacker = User::factory()->create();
        Wallet::factory(['user_id' => $attacker->id, 'balance' => 200])->create();

        $this->actingAs($attacker)
            ->postJson(route('invoices.pay', $invoice))
            ->assertStatus(403);

        $this->assertNull($invoice->fresh()->paid_at);
    }

    public function test_cannot_pay_expired_invoice(): void
    {
        $user = User::factory()
            ->has(Wallet::factory(['balance' => 200]))
            ->has(Invoice::factory(['amount' => 100, 'expires_at' => Carbon::now()->subDay()]))
            ->create();

        $invoice = $user->invoices->first();

        $this->actingAs($user)
            ->postJson(route('invoices.pay', $invoice))
            ->assertStatus(500)
            ->assertJson(['message' => 'Invoice has expired.']);
    }

    public function test_daily_limit_blocks_payment(): void
    {
        config(['wallet.daily_limit' => 300]);

        $user = User::factory()
            ->has(Wallet::factory(['balance' => 500]))
            ->has(Invoice::factory(['amount' => 100, 'paid_at' => now()])->count(3))
            ->create();

        $invoice = Invoice::factory(['user_id' => $user->id, 'amount' => 50])->create();

        $this->actingAs($user)
            ->postJson(route('invoices.pay', $invoice))
            ->assertStatus(500)
            ->assertJson(['message' => 'Daily spending limit reached.']);

        $this->assertNull($invoice->fresh()->paid_at);
    }
}
