<?php

namespace Database\Factories\Document;

use App\Models\Document\FileType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document\FileType>
 */
class FileTypeFactory extends Factory
{
    protected $model = FileType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Predefined realistic file type names
        static $fileTypeNames = [
            'Identity Card',
            'Passport',
            'Birth Certificate',
            'Marriage Certificate',
            'Residence Permit',
            'Academic Transcript',
            'Driver License',
            'Medical Report',
            'Employment Contract',
            'Tax Declaration',
            'Insurance Certificate',
            'Criminal Record',
        ];

        // Pick one unique name per factory call
        $name = $this->faker->randomElement($fileTypeNames);

        return [
            'name' => $name,
            'description' => $this->faker->sentence(8),
        ];
    }
}
