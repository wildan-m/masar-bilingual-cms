<?php

namespace App\Support\Localization;

use Carbon\CarbonInterface;

/**
 * Visitor-facing date formatting in the firm's time zone and the current
 * language (Arabic month names on Arabic pages).
 */
final class Dates
{
    public static function long(?CarbonInterface $date): string
    {
        if ($date === null) {
            return '';
        }

        return $date->copy()
            ->setTimezone(config('masar.display_timezone'))
            ->locale(Locale::current()->value)
            ->translatedFormat('j F Y');
    }

    public static function iso(?CarbonInterface $date): string
    {
        return $date?->toAtomString() ?? '';
    }
}
