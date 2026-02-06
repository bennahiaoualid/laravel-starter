<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // In production, only run essential seeders
        if (app()->environment('production', 'prod')) {
            $this->call([
                RoleSeeder::class,
                ProductUnitSeeder::class,
            ]);

            return;
        }

        // In other environments, run all seeders
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ProductUnitSeeder::class,
            ProductSeeder::class,
            BankSeeder::class,
            PartieSeeder::class,
            InvestorSeeder::class,
            SystemSettingSeeder::class,
            InvoiceSeeder::class,
            PurchaseOrderSeeder::class,
            CostSeeder::class,
            BoxSeeder::class,
            FileTypeSeeder::class,
        ]);

    }
}
