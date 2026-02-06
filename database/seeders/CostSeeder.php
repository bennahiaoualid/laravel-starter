<?php

namespace Database\Seeders;

use App\Models\Transaction\Cost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('CostSeeder: No users found. General costs require users for the responsible field.');

            return;
        }

        $costDescriptions = [
            'Office Rent',
            'Electricity Bill',
            'Water Bill',
            'Internet Subscription',
            'Phone Bill',
            'Office Supplies',
            'Maintenance Cost',
            'Cleaning Service',
            'Security Service',
            'Marketing Expenses',
            'Advertising Cost',
            'Professional Services',
            'Legal Fees',
            'Accounting Services',
            'Insurance Premium',
            'Vehicle Maintenance',
            'Fuel Cost',
            'Equipment Purchase',
            'Software License',
            'Training Cost',
        ];

        // Create 20-30 general costs
        $costsCount = rand(20, 30);

        for ($i = 0; $i < $costsCount; $i++) {
            $amount = rand(500, 50000) / 10; // 50.0 - 5000.0
            $responsible = $users->random()->name;
            $description = $costDescriptions[array_rand($costDescriptions)];
            $note = rand(0, 1) ? 'Monthly payment' : null; // 50% chance of having a note

            // Random date within the last 90 days
            $date = Carbon::now()->subDays(rand(0, 90));

            Cost::create([
                'amount' => round($amount, 2),
                'responsible' => $responsible,
                'note' => $note,
                'date' => $date,
            ]);
        }

        $this->command->info("Created {$costsCount} general costs.");
    }
}
