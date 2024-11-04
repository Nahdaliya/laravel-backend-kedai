<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->numberBetween(10000,10000),
            'stock' => $this->faker->numberBetween(1,100),
            'category' => $this->faker->randomElement(['food', 'drinks', 'Snacks']),
            'image' => $this->faker->imageUrl(640, 480),
        ];
    }
}

// 'category' => $this->faker->randomElement(['food', 'drinks', 'snacks']),
