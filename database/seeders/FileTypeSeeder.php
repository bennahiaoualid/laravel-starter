<?php

namespace Database\Seeders;

use App\Models\Document\FileType;
use Illuminate\Database\Seeder;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileTypeNames = [
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
        ];

        foreach ($fileTypeNames as $name) {
            FileType::firstOrCreate([
                'name' => $name,
            ], [
                'description' => 'Official document: '.$name,
            ]);
        }
    }
}
