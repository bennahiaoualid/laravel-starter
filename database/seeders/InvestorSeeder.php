<?php

namespace Database\Seeders;

use App\Models\Person\Investor;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class InvestorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = FakerFactory::create();

        // Create 5-10 investors
        $investorsCount = rand(5, 10);

        for ($i = 1; $i <= $investorsCount; $i++) {
            Investor::create([
                'name' => $faker->company().' Investor',
                'address' => $faker->address(),
                'phone' => $faker->phoneNumber(),
                'email' => $faker->safeEmail(),
                'init_balance' => rand(10000, 100000) / 100, // 100.00 - 1000.00
                'balance' => rand(10000, 100000) / 100, // 100.00 - 1000.00
            ]);
        }

        $this->command->info("Created {$investorsCount} investors.");
    }
}
