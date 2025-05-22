<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\PaymentConfirmation;
use App\Models\User;
use App\Notifications\TwoStepCodeNotification;
use App\Services\TwoStepVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TwoStepVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_initiate_sends_code_and_saves_hashed_record(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $invoice = Invoice::factory()->for($user)->create();

        $this->actingAs($user)
            ->postJson('/api/invoice/' . $invoice->id . '/2sv/initiate')
            ->assertOk();

        Notification::assertSentTo($user, TwoStepCodeNotification::class);

        $this->assertDatabaseHas('payment_confirmations', [
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'confirmed' => false,
        ]);
    }

    public function test_verify_accepts_correct_code(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->for($user)->create();

        $service = new TwoStepVerificationService();
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('initiate');
        $method->invoke($service, $user, $invoice);

        $confirmation = PaymentConfirmation::where('user_id', $user->id)->latest()->first();

        $code = '123456';
        $confirmation->update(['code' => bcrypt($code)]);

        $this->actingAs($user)
            ->postJson('/api/invoice/' . $invoice->id . '/2sv/verify', ['code' => $code])
            ->assertOk();

        $this->assertDatabaseHas('payment_confirmations', [
            'id' => $confirmation->id,
            'confirmed' => true,
        ]);
    }

    public function test_verify_fails_on_wrong_code(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->for($user)->create();

        $service = new TwoStepVerificationService();
        $service->initiate($user, $invoice);

        $this->actingAs($user)
            ->postJson('/api/invoice/' . $invoice->id . '/2sv/verify', ['code' => 'wrong'])
            ->assertStatus(422);
    }

    public function test_code_expires(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->for($user)->create();

        PaymentConfirmation::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'code' => bcrypt('654321'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($user)
            ->postJson('/api/invoice/' . $invoice->id . '/2sv/verify', ['code' => '654321'])
            ->assertStatus(422);
    }
}
