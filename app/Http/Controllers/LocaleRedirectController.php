<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Support\Localization\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Sends visitors of "/" to the home page in their language: the language
 * they chose before (cookie), else the best match of their browser's
 * Accept-Language header, else English.
 */
final class LocaleRedirectController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $locale = Locale::tryFrom((string) $request->cookie(SetLocale::COOKIE))
            ?? Locale::tryFrom((string) $request->getPreferredLanguage(Locale::codes()))
            ?? Locale::fallback();

        $response = redirect()->route('site.home', ['locale' => $locale->value]);
        $response->setVary('Accept-Language, Cookie');

        return $response;
    }
}
