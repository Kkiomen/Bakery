<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'nazwa' => $this->faker->words(2, true),
            'opis' => $this->faker->sentence(),
            'aktywny' => true,
        ];
    }
}
