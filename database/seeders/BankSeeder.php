<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'name' => [
                    'ar' => 'البنك الوطني الجزائري',
                    'en' => 'National Bank of Algeria',
                    'fr' => 'Banque Nationale d\'Algérie',
                ],
            ],
            [
                'name' => [
                    'ar' => 'بنك الجزائر الخارجي',
                    'en' => 'Banque Extérieure d\'Algérie',
                    'fr' => 'Banque Extérieure d\'Algérie',
                ],
            ],
            [
                'name' => [
                    'ar' => 'البنك الشعبي الجزائري',
                    'en' => 'Popular Bank of Algeria',
                    'fr' => 'Banque Populaire d\'Algérie',
                ],
            ],
            [
                'name' => [
                    'ar' => 'بنك التنمية المحلية',
                    'en' => 'Local Development Bank',
                    'fr' => 'Banque de Développement Local',
                ],
            ],
            [
                'name' => [
                    'ar' => 'البنك الزراعي والريفي',
                    'en' => 'Agricultural and Rural Bank',
                    'fr' => 'Banque de l\'Agriculture et du Développement Rural',
                ],
            ],
            [
                'name' => [
                    'ar' => 'بنك الخليفة',
                    'en' => 'Khalifa Bank',
                    'fr' => 'Banque Khalifa',
                ],
            ],
            [
                'name' => [
                    'ar' => 'بنك الجزيرة',
                    'en' => 'Al Jazeera Bank',
                    'fr' => 'Banque Al Jazeera',
                ],
            ],
            [
                'name' => [
                    'ar' => 'بنك السلام',
                    'en' => 'Salam Bank',
                    'fr' => 'Banque Salam',
                ],
            ],
            [
                'name' => [
                    'ar' => 'بنك البركة',
                    'en' => 'Al Baraka Bank',
                    'fr' => 'Banque Al Baraka',
                ],
            ],
            [
                'name' => [
                    'ar' => 'بنك الإسكان',
                    'en' => 'Housing Bank',
                    'fr' => 'Banque de l\'Habitat',
                ],
            ],
        ];

        foreach ($banks as $bankData) {
            Bank::create($bankData);
        }
    }
}
