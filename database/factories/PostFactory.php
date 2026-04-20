<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'   => $this->faker->words(4, true),
            'status'  => $this->faker->randomElement(['draft', 'published']),
            'content' => $this->faker->paragraphs(5, true),
            'user_id' => User::factory(),
        ];
    }
}
