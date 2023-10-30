<?php 
// ProductFactory.php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'category_id' => rand(1, 5), // Adjust the range based on your category IDs
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence,
            'image' => $this->faker->imageUrl(),
            'amount' => $this->faker->numberBetween(1, 100),
            'rating_average' => $this->faker->randomFloat(1, 1, 5),
            'specifications' => [
                'size' => $this->faker->randomElement(['small', 'medium', 'large']),
                'color' => $this->faker->safeColorName,
            ],
            'highlight' => [
                'feature_1' => $this->faker->word,
                'feature_2' => $this->faker->word,
            ],
        ];
    }
}
