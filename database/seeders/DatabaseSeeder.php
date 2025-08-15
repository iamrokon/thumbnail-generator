<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tiers = config('tiers');

        foreach ($tiers as $tierName => $settings) {
            // Generate 5 random users for this tier
            User::factory()->count(5)->create([
                'tier' => $tierName,
            ]);

            // Create one fixed/test user for this tier
            User::factory()->create([
                'name' => ucfirst($tierName) . ' User',
                'email' => strtolower($tierName) . '@example.com',
                'tier' => $tierName,
                'password' => bcrypt('password'), // default password
            ]);
        }
    }
}
