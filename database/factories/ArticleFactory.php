<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\NewsCategory;
use Database\Factories\Concerns\GeneratesBilingualText;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
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
            'excerpt' => $this->bilingualParagraph(),
            'body' => array_map(fn (string $text): string => "<p>{$text}</p>", $this->bilingualParagraph()),
            'news_category_id' => NewsCategory::factory(),
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
        ];
    }

    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }

    public function scheduled(): static
    {
        return $this->state(['published_at' => now()->addWeek()]);
    }
}
