<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $gender   = fake()->randomElement(['male', 'female', 'non_binary', 'other']);
        $seeking  = fake()->randomElement(['male', 'female', 'everyone']);

        $firstName  = fake()->firstName($gender === 'female' ? 'female' : 'male');
        $lastName   = fake()->lastName();
        $username   = strtolower($firstName) . fake()->numberBetween(10, 9999);

        return [
            'name'               => "$firstName $lastName",
            'username'           => $username,
            'email'              => fake()->unique()->safeEmail(),
            'email_verified_at'  => now(),
            'password'           => static::$password ??= Hash::make('password'),
            'remember_token'     => Str::random(10),
            'gender'             => $gender,
            'seeking'            => $seeking,
            'date_of_birth'      => fake()->dateTimeBetween('-55 years', '-18 years')->format('Y-m-d'),
            'profile_complete'   => true,
            'onboarding_step'    => 5,
            'last_active_at'     => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
