<?php

namespace Database\Factories\Person;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person\Partie>
 */
class PartieFactory extends Factory
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
                'ar' => $this->faker->company(),
                'en' => $this->faker->company(),
                'fr' => $this->faker->company(),
            ],
            'nif' => $this->faker->numerify(str_repeat('#', rand(15, 20))), // 15-20 numbers
            'nis' => $this->faker->numerify('###############'), // 15 numbers
            'art' => $this->faker->numerify('###########'), // 11 numbers
            'rc' => [
                'ar' => $this->faker->numerify('######').'/'.$this->faker->randomElement(['A', 'B', '01', '02']), // 6 numbers/A or B or translation - two numbers
                'en' => $this->faker->numerify('######').'/'.$this->faker->randomElement(['A', 'B', '01', '02']),
                'fr' => $this->faker->numerify('######').'/'.$this->faker->randomElement(['A', 'B', '01', '02']),
            ],
            'mf' => $this->faker->numerify('##########'), // 10 numbers
            'field_id' => Field::factory(),
            'address' => [
                'ar' => $this->faker->address(),
                'en' => $this->faker->address(),
                'fr' => $this->faker->address(),
            ],
            'phone' => $this->faker->phoneNumber(),
            'bank_id' => null, // Will be set in seeder if needed
            'bank_account' => null, // Will be set in seeder if needed
            'initial_debt' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}
