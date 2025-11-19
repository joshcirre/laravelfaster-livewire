<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subcategory>
 */
class SubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(rand(2, 3), true);

        return [
            'slug' => \Illuminate\Support\Str::slug($name),
            'name' => ucwords($name),
            'subcollection_id' => \App\Models\Subcollection::factory(),
            'image_url' => null,
        ];
    }
}
