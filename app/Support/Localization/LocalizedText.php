<?php

namespace App\Support\Localization;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * An immutable set of translations for a single attribute, keyed by locale.
 *
 * Reading a locale without a translation never yields an empty string while
 * another translation exists: it falls back to English first and then to any
 * other available locale. Callers can ask whether a fallback happened so the
 * page can mark the text with the language it is really written in.
 *
 * @implements Arrayable<string, string|null>
 */
final class LocalizedText implements Arrayable, JsonSerializable, Stringable
{
    /**
     * Non-blank translations in canonical locale order.
     *
     * @var array<string, string>
     */
    private readonly array $values;

    /**
     * @param  array<array-key, mixed>  $values
     */
    public function __construct(array $values = [])
    {
        $normalized = [];

        foreach (Locale::cases() as $locale) {
            $value = $values[$locale->value] ?? null;

            if (is_string($value) && ! self::isBlank($value)) {
                $normalized[$locale->value] = trim($value);
            }
        }

        $this->values = $normalized;
    }

    /**
     * Build an instance from any value an attribute may be assigned.
     *
     * A plain (non-JSON) string is treated as the translation for the current
     * locale, which mirrors how a single-language form would submit it.
     */
    public static function make(mixed $value): self
    {
        return match (true) {
            $value instanceof self => $value,
            $value === null, $value === '' => new self,
            is_array($value) => new self($value),
            is_string($value) => self::fromString($value),
            default => throw new InvalidArgumentException('A localized value must be an array, a JSON string or a string.'),
        };
    }

    /**
     * A JSON object of translations, or a plain value for the current locale.
     */
    private static function fromString(string $value): self
    {
        $decoded = json_decode($value, true);

        return is_array($decoded) ? new self($decoded) : new self([Locale::current()->value => $value]);
    }

    public static function fromJson(?string $json): self
    {
        if ($json === null || $json === '') {
            return new self;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? new self($decoded) : new self([Locale::fallback()->value => $json]);
    }

    /**
     * The translation for the locale, falling back when it is missing.
     */
    public function get(Locale|string|null $locale = null): string
    {
        $resolved = $this->resolveLocale($locale);

        return $resolved === null ? '' : $this->values[$resolved->value];
    }

    /**
     * The exact translation for the locale, without any fallback.
     */
    public function in(Locale|string $locale): ?string
    {
        return $this->values[Locale::resolve($locale)->value] ?? null;
    }

    public function has(Locale|string $locale): bool
    {
        return array_key_exists(Locale::resolve($locale)->value, $this->values);
    }

    /**
     * The locale whose text get() returns, or null when nothing is translated.
     */
    public function resolveLocale(Locale|string|null $locale = null): ?Locale
    {
        $requested = Locale::resolve($locale);

        if ($this->has($requested)) {
            return $requested;
        }

        if ($this->has(Locale::fallback())) {
            return Locale::fallback();
        }

        foreach (Locale::cases() as $candidate) {
            if ($this->has($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Whether reading the locale returns text written in another language.
     */
    public function isFallback(Locale|string|null $locale = null): bool
    {
        $resolved = $this->resolveLocale($locale);

        return $resolved !== null && $resolved !== Locale::resolve($locale);
    }

    public function isEmpty(): bool
    {
        return $this->values === [];
    }

    /**
     * @return list<Locale>
     */
    public function missingLocales(): array
    {
        return array_values(array_filter(Locale::cases(), fn (Locale $locale): bool => ! $this->has($locale)));
    }

    public function with(Locale|string $locale, ?string $value): self
    {
        return new self([...$this->values, Locale::resolve($locale)->value => $value]);
    }

    public function equals(self $other): bool
    {
        return $this->values === $other->values;
    }

    /**
     * Every supported locale, with null for missing translations. This is the
     * shape admin forms are filled with (`title.en`, `title.ar`).
     *
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        $translations = [];

        foreach (Locale::cases() as $locale) {
            $translations[$locale->value] = $this->values[$locale->value] ?? null;
        }

        return $translations;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return $this->values;
    }

    /**
     * The storage representation, or null when nothing is translated.
     */
    public function toJson(): ?string
    {
        if ($this->isEmpty()) {
            return null;
        }

        return json_encode($this->values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * Rich text editors leave markup such as "<p></p>" behind when a field is
     * cleared, so markup without visible content counts as blank.
     */
    public static function isBlank(string $value): bool
    {
        $text = html_entity_decode(strip_tags($value, '<img><iframe><video>'), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(str_replace("\u{00A0}", ' ', $text)) === '';
    }
}
