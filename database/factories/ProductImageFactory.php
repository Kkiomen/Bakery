<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'filename' => Str::uuid() . '.jpg',
            'original_name' => $this->faker->word . '.jpg',
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(100000, 5000000),
            'alt_text' => $this->faker->sentence(3),
            'sort_order' => $this->faker->numberBetween(1, 10),
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
