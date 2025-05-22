<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'expires_at' => Carbon::now()->addDays(1),
            'paid_at' => null,
        ];
    }

    public function paid(): self
    {
        return $this->state(fn () => [
            'paid_at' => Carbon::now(),
        ]);
    }

    public function expired(): self
    {
        return $this->state(fn () => [
            'expires_at' => Carbon::now()->subDay(),
        ]);
    }
}
