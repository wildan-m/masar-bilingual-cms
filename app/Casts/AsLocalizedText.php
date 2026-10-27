<?php

namespace App\Casts;

use App\Support\Localization\LocalizedText;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\ComparesCastableAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Stores translations as a JSON object ({"en": "...", "ar": "..."}) and
 * exposes them as a LocalizedText value object.
 *
 * @implements CastsAttributes<LocalizedText, mixed>
 */
final class AsLocalizedText implements CastsAttributes, ComparesCastableAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): LocalizedText
    {
        return LocalizedText::fromJson(is_string($value) ? $value : null);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return LocalizedText::make($value)->toJson();
    }

    /**
     * Compare decoded translations so that a database which normalises JSON
     * (key order, whitespace) does not make unchanged records look dirty.
     */
    public function compare(Model $model, string $key, mixed $firstValue, mixed $secondValue): bool
    {
        return LocalizedText::make($firstValue)->equals(LocalizedText::make($secondValue));
    }
}
