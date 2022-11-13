<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'admin_id' => Admin::inRandomOrder()->first()->id,
            'subject' => substr($this->faker->sentence(), 0, 64),
            'content' => $this->faker->text(2000)
        ];
    }
}
