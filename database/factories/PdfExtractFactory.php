<?php

namespace Database\Factories;

use App\Models\PdfExtract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PdfExtract>
 */
class PdfExtractFactory extends Factory
{
    protected $model = PdfExtract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_type' => \App\Models\Invoice\Invoice::class,
            'document_id' => 1,
            'data' => hash('sha256', json_encode([])),
            'local_url' => null,
            'status' => 'pending',
        ];
    }
}
