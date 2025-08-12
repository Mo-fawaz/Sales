<?php

namespace Database\Seeders;

use App\Models\Houses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HousesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Houses::factory()->count(10)->create();

    }
}
