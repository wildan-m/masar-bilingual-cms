<?php

namespace App\Support\Settings;

use App\Models\Setting;
use App\Support\Localization\LocalizedText;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Typed access to site-wide settings (company details, contact information,
 * social links, home page copy).
 *
 * Values live in the `settings` table, one row per key, and are cached as a
 * whole. Keys listed in LOCALIZED hold {"en": ..., "ar": ...} objects.
 */
class SiteSettings
{
    private const CACHE_KEY = 'site-settings';

    /**
     * Settings shown in more than one language.
     */
    public const LOCALIZED = [
        'company_name',
        'tagline',
        'address',
        'office_hours',
        'hero_title',
        'hero_subtitle',
        'default_meta_description',
    ];

    /**
     * Settings holding a single value regardless of language.
     */
    public const PLAIN = [
        'phone',
        'email',
        'proposals_email',
        'founded_year',
        'map_url',
        'linkedin_url',
        'instagram_url',
        'x_url',
        'youtube_url',
    ];

    /**
     * @var array<string, mixed>|null
     */
    private ?array $values = null;

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->all(), $key) ?? $this->defaults()[$key] ?? $default;
    }

    /**
     * Values used until an editor saves the setting, so a fresh installation
     * never renders an empty title.
     *
     * @return array<string, mixed>
     */
    private function defaults(): array
    {
        return [
            'company_name' => ['en' => config('app.name')],
        ];
    }

    public function text(string $key): LocalizedText
    {
        return LocalizedText::make($this->get($key));
    }

    /**
     * Non-empty social profile links keyed by network.
     *
     * @return array<string, string>
     */
    public function socialLinks(): array
    {
        return array_filter([
            'linkedin' => $this->get('linkedin_url'),
            'instagram' => $this->get('instagram_url'),
            'x' => $this->get('x_url'),
            'youtube' => $this->get('youtube_url'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values ??= Cache::rememberForever(
            self::CACHE_KEY,
            fn (): array => Setting::query()->pluck('value', 'key')->all(),
        );
    }

    /**
     * The form representation: every key present, translations as arrays.
     *
     * @return array<string, mixed>
     */
    public function formState(): array
    {
        $state = [];

        foreach (self::LOCALIZED as $key) {
            $state[$key] = $this->text($key)->toArray();
        }

        foreach (self::PLAIN as $key) {
            $state[$key] = $this->get($key);
        }

        return $state;
    }

    /**
     * Persist the given settings. Unknown keys are ignored; unchanged keys are
     * not written, so the activity log only lists real changes.
     *
     * @param  array<string, mixed>  $values
     */
    public function save(array $values): void
    {
        DB::transaction(function () use ($values): void {
            foreach (Arr::only($values, [...self::LOCALIZED, ...self::PLAIN]) as $key => $value) {
                $value = in_array($key, self::LOCALIZED, true)
                    ? LocalizedText::make($value)->jsonSerialize() ?: null
                    : (blank($value) ? null : $value);

                $setting = Setting::query()->firstOrNew(['key' => $key]);
                $setting->value = $value;

                if (! $setting->exists || $setting->isDirty('value')) {
                    $setting->save();
                }
            }
        });

        $this->flush();
    }

    public function flush(): void
    {
        $this->values = null;

        Cache::forget(self::CACHE_KEY);
    }
}
