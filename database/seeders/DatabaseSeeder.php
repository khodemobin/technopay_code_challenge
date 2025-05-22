<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\User;

use App\Models\Wallet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->has(Wallet::factory())
            ->has(Invoice::factory()->count(5))
            ->count(10)
            ->create();
    }
}
