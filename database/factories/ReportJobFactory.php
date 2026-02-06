<?php

namespace Database\Factories;

use App\Models\ReportJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportJob>
 */
class ReportJobFactory extends Factory
{
    protected $model = ReportJob::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'status' => ReportJob::STATUS_PENDING,
            'results' => null,
            'error_message' => null,
        ];
    }
}
