<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            'Damascus',
            'Aleppo',
            'Homs',
            'Hama',
            'Latakia',
            'Tartous',
            'Daraa',
            'Al-Hasakah',
            'Deir ez-Zor',
            'Raqqa',
            'Idlib',
            'Quneitra'
        ];

        foreach ($cities as $value) {
            DB::table('cities')->insert([
                'name' => $value,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
