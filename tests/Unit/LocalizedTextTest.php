<?php

namespace Tests\Unit;

use App\Support\Localization\Locale;
use App\Support\Localization\LocalizedSlug;
use App\Support\Localization\LocalizedText;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LocalizedTextTest extends TestCase
{
    public function test_it_keeps_supported_non_blank_translations_in_a_fixed_order(): void
    {
        $text = new LocalizedText(['ar' => ' عنوان ', 'fr' => 'Titre', 'en' => 'Title']);

        $this->assertSame(['en' => 'Title', 'ar' => 'عنوان'], $text->jsonSerialize());
        $this->assertSame('{"en":"Title","ar":"عنوان"}', $text->toJson());
    }

    public function test_missing_arabic_falls_back_to_english_and_says_so(): void
    {
        $text = new LocalizedText(['en' => 'Structural engineering']);

        $this->assertSame('Structural engineering', $text->get(Locale::Arabic));
        $this->assertTrue($text->isFallback(Locale::Arabic));
        $this->assertSame(Locale::English, $text->resolveLocale(Locale::Arabic));
        $this->assertNull($text->in(Locale::Arabic));
        $this->assertFalse($text->isFallback(Locale::English));
    }

    public function test_it_never_returns_a_blank_string_while_any_translation_exists(): void
    {
        $text = new LocalizedText(['ar' => 'الهندسة الإنشائية']);

        $this->assertSame('الهندسة الإنشائية', $text->get(Locale::English));
        $this->assertTrue($text->isFallback(Locale::English));
        $this->assertSame('', (new LocalizedText)->get(Locale::Arabic));
    }

    public function test_markup_without_visible_text_counts_as_missing(): void
    {
        $text = new LocalizedText(['en' => '<p>Body</p>', 'ar' => '<p>&nbsp;</p><p></p>']);

        $this->assertFalse($text->has(Locale::Arabic));
        $this->assertSame(['en' => '<p>Body</p>', 'ar' => null], $text->toArray());
    }

    public function test_a_plain_string_is_stored_as_the_current_locale(): void
    {
        App::setLocale('ar');

        $this->assertSame(['ar' => 'مسار'], LocalizedText::make('مسار')->jsonSerialize());
        $this->assertSame(['en' => 'Masar'], LocalizedText::make('{"en":"Masar"}')->jsonSerialize());
    }

    public function test_string_conversion_uses_the_current_locale(): void
    {
        $text = new LocalizedText(['en' => 'Projects', 'ar' => 'المشاريع']);

        App::setLocale('ar');
        $this->assertSame('المشاريع', (string) $text);

        App::setLocale('en');
        $this->assertSame('Projects', (string) $text);
    }

    public function test_arabic_slugs_keep_the_arabic_script(): void
    {
        $this->assertSame('الهندسة-الإنشائية', LocalizedSlug::make('الهندسة الإنشائيّة', Locale::Arabic));
        $this->assertSame('structural-engineering', LocalizedSlug::make('Structural  Engineering!', Locale::English));
        $this->assertTrue(LocalizedSlug::isValid('مشاريع-2026'));
        $this->assertFalse(LocalizedSlug::isValid('two--hyphens'));
        $this->assertFalse(LocalizedSlug::isValid('slash/inside'));
    }
}
