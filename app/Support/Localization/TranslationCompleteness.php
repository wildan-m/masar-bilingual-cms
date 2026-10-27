<?php

namespace App\Support\Localization;

/**
 * How many of a record's required translatable attributes exist in a locale.
 */
final readonly class TranslationCompleteness
{
    /**
     * @param  list<string>  $missing  Attribute names without a translation.
     */
    public function __construct(
        public Locale $locale,
        public int $total,
        public array $missing,
    ) {}

    public function filled(): int
    {
        return $this->total - count($this->missing);
    }

    public function isComplete(): bool
    {
        return $this->missing === [];
    }

    public function isUntranslated(): bool
    {
        return $this->total > 0 && $this->filled() === 0;
    }

    public function percentage(): int
    {
        return $this->total === 0 ? 100 : intdiv($this->filled() * 100, $this->total);
    }

    public function summary(): string
    {
        return "{$this->filled()}/{$this->total}";
    }
}
