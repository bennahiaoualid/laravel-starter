<?php

namespace Database\Factories\Document;

use App\Models\Document\Box;
use App\Models\Document\Document;
use App\Models\Person\Partie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document\Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partie_id' => Partie::factory(),
            'box_id' => Box::factory(),
        ];
    }
}
