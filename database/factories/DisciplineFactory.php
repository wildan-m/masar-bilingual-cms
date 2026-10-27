<?php

namespace Database\Factories;

use App\Models\Discipline;
use Database\Factories\Concerns\GeneratesBilingualText;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discipline>
 */
class DisciplineFactory extends Factory
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
            'summary' => $this->bilingualParagraph(),
            'body' => array_map(fn (string $text): string => "<p>{$text}</p>", $this->bilingualParagraph()),
            'icon' => fake()->randomElement(array_keys(Discipline::ICONS)),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => true,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }
}
