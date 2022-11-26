<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => preg_replace('/[^a-zA-Z\x{0020}]/', '', $this->faker->firstName()),
            'surname' => preg_replace('/[^a-zA-Z\x{0020}]/', '', $this->faker->lastName()),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->e164PhoneNumber(),
            'address' => $this->faker->address(),
            'city' => preg_replace('/[^a-zA-Z\x{0020}]/', '', $this->faker->city()),
            'state' => preg_replace('/[^a-zA-Z\x{0020}]/', '', $this->faker->country()),
            'zipcode' => $this->faker->numberBetween(1000000000, 9999999999),
            'password' => $this->faker->password(8, 20),
            'email_verified_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
