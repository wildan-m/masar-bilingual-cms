<?php

namespace Database\Factories;

use App\Enums\ClientType;
use App\Models\Project;
use App\Models\Sector;
use Database\Factories\Concerns\GeneratesBilingualText;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    use GeneratesBilingualText;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->bilingualTitle();

        return [
            'title' => $title,
            'slug' => $this->slugsFor($title),
            'location' => ['en' => 'Abu Dhabi, UAE', 'ar' => 'أبوظبي، الإمارات'],
            'summary' => $this->bilingualParagraph(),
            'body' => array_map(fn (string $text): string => "<p>{$text}</p>", $this->bilingualParagraph()),
            'sector_id' => Sector::factory(),
            'client_type' => fake()->randomElement(ClientType::cases()),
            'completion_year' => fake()->numberBetween(2015, (int) now()->format('Y')),
            'is_featured' => false,
            'is_published' => true,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    /**
     * English content only, as if the Arabic translation has not been written.
     */
    public function withoutArabic(): static
    {
        return $this->state(fn (array $attributes): array => [
            'title' => ['en' => $attributes['title']['en']],
            'slug' => ['en' => $attributes['slug']['en']],
            'location' => ['en' => $attributes['location']['en']],
            'summary' => ['en' => $attributes['summary']['en']],
            'body' => ['en' => $attributes['body']['en']],
        ]);
    }
}
