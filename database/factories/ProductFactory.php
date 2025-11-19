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
        $name = $this->faker->words(rand(2, 5), true);

        return [
            'slug' => \Illuminate\Support\Str::slug($name),
            'name' => ucwords($name),
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'subcategory_slug' => \App\Models\Subcategory::factory(),
            'image_url' => null,
        ];
    }
}
