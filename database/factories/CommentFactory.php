<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'comment' => $this->faker->words(10, true),
            'post_id' => null,
            'user_id' => null,
        ];
    }
}
