<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->text,
            'type' => $this->faker->randomElement(['news', 'scholarship', 'announcement']),
            'is_published' => $this->faker->boolean(80), // 80% chance of being published
        ];
    }
}
