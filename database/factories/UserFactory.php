<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserFactory extends Factory
{
    protected static ?string $password;


    protected $model = User::class;

    public function definition()
    {
        $faker = \Faker\Factory::create('ar_SA');
        return [
            'first_name' => $faker->firstName($faker->randomElement(['male', 'female'])),
            'last_name' => $faker->lastName(),
            'phone' => $this->generateSaudiPhoneNumber(),
            'email' => $faker->unique()->safeEmail(),
            'password' => bcrypt('كلمةالسر'), // كلمة سر افتراضية
            'passport' => $this->generateArabicPassportNumber(),
            'nationality' => 'SA', // رمز السعودية
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
        ];
    }
    protected function generateSaudiPhoneNumber(): string
    {
        return '9665' . $this->faker->numerify('#######'); // مثال: 966512345678
    }

    protected function generateArabicPassportNumber(): string
    {
        return 'P' . $this->faker->numerify('######'); // مثال: P123456
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
