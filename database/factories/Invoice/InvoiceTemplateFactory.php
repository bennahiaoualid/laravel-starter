<?php

namespace Database\Factories\Invoice;

use App\Models\Invoice\InvoiceTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice\InvoiceTemplate>
 */
class InvoiceTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InvoiceTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->sentence(),
            'is_default' => false,
            'template_config' => [
                'header' => 'Sample Header',
                'footer' => 'Sample Footer',
                'fields' => [],
            ],
        ];
    }

    /**
     * Indicate that the template is the default one.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
