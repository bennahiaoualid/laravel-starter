<?php

namespace Database\Seeders;

use App\Models\Person\Partie;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class PartieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = FakerFactory::create();
        $banks = \App\Models\Bank::all();

        if ($banks->isEmpty()) {
            // If no banks exist, create parties without bank info
            // Create at least 2 my companies and 3 regular parties
            Partie::factory()->count(2)->create(['is_my_company' => true]);
            Partie::factory()->count(3)->create(['is_my_company' => false]);

            return;
        }

        // Create parties, some with bank information
        // Create at least 2 my companies and 3 regular parties
        $myCompaniesCount = 2;
        $regularPartiesCount = 3;

        // Create my companies
        foreach (range(1, $myCompaniesCount) as $i) {
            $partie = Partie::factory()->create([
                'is_my_company' => true,
            ]);

            // 70% chance to assign a bank
            if (rand(1, 10) <= 7) {
                $bank = $banks->random();
                $partie->update([
                    'bank_id' => $bank->id,
                    'bank_account' => $faker->numerify('################'), // 16-digit account number
                ]);
            }
        }

        // Create regular parties (not my companies)
        foreach (range(1, $regularPartiesCount) as $i) {
            $partie = Partie::factory()->create([
                'is_my_company' => false,
            ]);

            // 70% chance to assign a bank
            if (rand(1, 10) <= 7) {
                $bank = $banks->random();
                $partie->update([
                    'bank_id' => $bank->id,
                    'bank_account' => $faker->numerify('################'), // 16-digit account number
                ]);
            }
        }
    }
}
