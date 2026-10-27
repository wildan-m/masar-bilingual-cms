<?php

namespace Database\Factories;

use App\Enums\PageTemplate;
use App\Models\Page;
use Database\Factories\Concerns\GeneratesBilingualText;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    use GeneratesBilingualText;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->bilingualTitle();

        return [
            'key' => null,
            'template' => PageTemplate::Standard,
            'title' => $title,
            'slug' => $this->slugsFor($title),
            'body' => array_map(fn (string $text): string => "<p>{$text}</p>", $this->bilingualParagraph()),
            'is_published' => true,
        ];
    }

    public function about(): static
    {
        return $this->state([
            'key' => Page::ABOUT,
            'title' => ['en' => 'About us', 'ar' => 'من نحن'],
            'slug' => ['en' => 'about', 'ar' => 'من-نحن'],
        ]);
    }

    public function contact(): static
    {
        return $this->state([
            'key' => Page::CONTACT,
            'template' => PageTemplate::Contact,
            'title' => ['en' => 'Contact us', 'ar' => 'تواصل معنا'],
            'slug' => ['en' => 'contact', 'ar' => 'تواصل-معنا'],
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(['is_published' => false]);
    }
}
