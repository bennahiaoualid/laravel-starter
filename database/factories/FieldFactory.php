<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */
class FieldFactory extends Factory
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
                'ar' => $this->faker->randomElement(['حبوب', 'خضروات', 'فواكه', 'بذور', 'عضوي', 'أعلاف', 'أسمدة']),
                'en' => $this->faker->randomElement(['grain', 'vegetable', 'fruit', 'seed', 'organic', 'feed', 'fertilizer']),
                'fr' => $this->faker->randomElement(['céréale', 'légume', 'fruit', 'graine', 'organique', 'aliment', 'engrais']),
            ],
        ];
    }
}
