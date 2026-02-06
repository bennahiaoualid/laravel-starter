<?php

namespace Database\Factories;

use App\Models\Invoice\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryReceipt>
 */
class DeliveryReceiptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'num' => $this->faker->unique()->numerify('DR-####'),
            'delivery_receipt_date' => $this->faker->date(),
        ];
    }
}
