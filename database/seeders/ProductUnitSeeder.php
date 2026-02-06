<?php

namespace Database\Seeders;

use App\Models\Product\ProductUnit;
use Illuminate\Database\Seeder;

class ProductUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => [
                    'ar' => 'و',
                    'en' => 'U',
                    'fr' => 'U',
                ],
            ],
            [
                'name' => [
                    'ar' => 'كلغ',
                    'en' => 'kg',
                    'fr' => 'kg',
                ],
            ],
            [
                'name' => [
                    'ar' => 'يوم',
                    'en' => 'day',
                    'fr' => 'jour',
                ],
            ],
            [
                'name' => [
                    'ar' => 'ساعة',
                    'en' => 'hour',
                    'fr' => 'heure',
                ],
            ],
            [
                'name' => [
                    'ar' => 'لتر',
                    'en' => 'liter',
                    'fr' => 'litre',
                ],
            ],
        ];

        foreach ($units as $unitData) {
            ProductUnit::create($unitData);
        }
    }
}
