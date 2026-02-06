<?php

namespace Database\Factories\Transaction;

use App\Models\Person\Investor;
use App\Models\Person\Partie;
use App\Models\Transaction\MoneyTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction\MoneyTransaction>
 */
class MoneyTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partable_id' => Partie::factory(),
            'partable_type' => Partie::class,
            'amount' => $this->faker->randomFloat(2, 10, 10000),
            'type' => $this->faker->randomElement([MoneyTransaction::TYPE_IN, MoneyTransaction::TYPE_OUT]),
            'is_debt' => $this->faker->boolean(),
            'note' => $this->faker->optional()->sentence(),
            'transaction_date' => $this->faker->date('Y-m-d'),
        ];
    }

    /**
     * Indicate that the transaction is for a partie.
     */
    public function forPartie(?Partie $partie = null): static
    {
        return $this->state(function (array $attributes) use ($partie) {
            $partie = $partie ?? Partie::factory()->create(['is_my_company' => false]);

            return [
                'partable_id' => $partie->id,
                'partable_type' => Partie::class,
            ];
        });
    }

    /**
     * Indicate that the transaction is for an investor.
     */
    public function forInvestor(?Investor $investor = null): static
    {
        return $this->state(function (array $attributes) use ($investor) {
            $investor = $investor ?? Investor::factory()->create();

            return [
                'partable_id' => $investor->id,
                'partable_type' => Investor::class,
            ];
        });
    }

    /**
     * Indicate that the transaction is a debt transaction.
     */
    public function debt(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_debt' => true,
        ]);
    }

    /**
     * Indicate that the transaction is not a debt transaction.
     */
    public function notDebt(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_debt' => false,
        ]);
    }

    /**
     * Indicate that the transaction type is 'in'.
     */
    public function typeIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MoneyTransaction::TYPE_IN,
        ]);
    }

    /**
     * Indicate that the transaction type is 'out'.
     */
    public function typeOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MoneyTransaction::TYPE_OUT,
        ]);
    }
}
