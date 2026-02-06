<?php

namespace Database\Seeders;

use App\Models\Document\Box;
use Illuminate\Database\Seeder;

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Box::factory()->count(10)->create();
    }
}
