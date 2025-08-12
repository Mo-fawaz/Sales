<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Houses;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */

class HousesFactory extends Factory
{
    protected $model = Houses::class;

    public function definition()
    {
        $faker = \Faker\Factory::create('ar_SA'); // استخدام الإصدار العربي

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'title' => $faker->sentence(3),
            'description' => $faker->paragraph,
            'price_per_night' => $faker->numberBetween(50, 500),
            'amenities' => ['wifi', 'parking', 'pool'], // مصفوفة افتراضية
            'location' => $faker->address,
            'image' => json_encode([
                'https://picsum.photos/640/480?random=' . $this->faker->numberBetween(1, 1000),
                'https://picsum.photos/640/480?random=' . $this->faker->numberBetween(1, 1000)
            ]),
        ];
    }
}