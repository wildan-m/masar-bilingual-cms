<?php

namespace Database\Factories;

use App\Models\NewsCategory;
use Database\Factories\Concerns\GeneratesBilingualText;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsCategory>
 */
class NewsCategoryFactory extends Factory
{
    use GeneratesBilingualText;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->bilingualTitle();

        return [
            'name' => $name,
            'slug' => $this->slugsFor($name),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
