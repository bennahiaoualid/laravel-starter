<?php

namespace Database\Factories\PurchaseOrder;

use App\Models\Person\Partie;
use App\Models\PurchaseOrder\PurchaseOrder;
use App\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Partie::factory()->state(['is_my_company' => true]),
            'supplier_id' => Partie::factory()->state(['is_my_company' => false]),
            'order_num' => 'PO-'.$this->faker->unique()->numberBetween(1000, 9999),
            'order_date' => $this->faker->date(),
            'total' => $this->faker->randomFloat(2, 100, 10000),
            'paid' => $this->faker->randomFloat(2, 0, 5000),
            'status' => $this->faker->randomElement([
                PurchaseOrderStatus::PENDING->value,
                PurchaseOrderStatus::COMPLETED->value,
            ]),
        ];
    }
}
