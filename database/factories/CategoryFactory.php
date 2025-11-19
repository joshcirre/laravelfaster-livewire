<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(rand(2, 4), true);

        return [
            'slug' => \Illuminate\Support\Str::slug($name),
            'name' => ucwords($name),
            'collection_id' => \App\Models\Collection::factory(),
            'image_url' => null,
        ];
    }
}
