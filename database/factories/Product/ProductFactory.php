<?php

namespace Database\Factories\Product;

use App\Models\Field;
use App\Models\Product\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomUnit = ProductUnit::inRandomOrder()->first();
        $randomType = Field::inRandomOrder()->first();

        return [
            'name' => [
                'ar' => $this->faker->words(2, true),
                'en' => $this->faker->words(2, true),
                'fr' => $this->faker->words(2, true),
            ],
            'type_id' => $randomType?->id ?? Field::factory(),
            'unit_id' => $randomUnit?->id ?? ProductUnit::factory(),
            'unit_price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
