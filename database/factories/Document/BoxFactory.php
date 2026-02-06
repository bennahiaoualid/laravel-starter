<?php

namespace Database\Factories\Document;

use App\Models\Document\Box;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document\Box>
 */
class BoxFactory extends Factory
{
    protected $model = Box::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 1; // Keep incrementing B-1, B-2, etc.

        return [
            'number' => 'B-'.$counter++,
            'description' => $this->faker->sentence(),
        ];
    }
}
