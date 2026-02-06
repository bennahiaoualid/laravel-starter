<?php

namespace Database\Factories\Product;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\ProductUnit>
 */
class ProductUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => [
                'ar' => $this->faker->randomElement(['كيلوغرام', 'طن', 'كيس', 'صندوق', 'قطعة']),
                'en' => $this->faker->randomElement(['kilogram', 'ton', 'bag', 'box', 'piece']),
                'fr' => $this->faker->randomElement(['kilogramme', 'tonne', 'sac', 'boîte', 'pièce']),
            ],
        ];
    }
}
