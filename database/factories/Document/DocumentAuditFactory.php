<?php

namespace Database\Factories\Document;

use App\Models\Document\DocumentAudit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document\DocumentAudit>
 */
class DocumentAuditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_id' => null,
            'action' => DocumentAudit::ACTION_DELETE,
            'partie_name' => 'Test Partie',
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
