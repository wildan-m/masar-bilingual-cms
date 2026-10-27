<?php

namespace App\Http\Middleware;

use App\Support\Localization\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the locale from the {locale} URL prefix.
 *
 * The prefix is the single source of truth for the language of a page, so
 * every URL is shareable and crawlable in each language. The choice is also
 * remembered in a cookie, which the root URL uses for returning visitors.
 */
final class SetLocale
{
    public const COOKIE = 'locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = Locale::tryFrom((string) $request->route('locale'));

        abort_if($locale === null, 404);

        App::setLocale($locale->value);
        URL::defaults(['locale' => $locale->value]);

        // Controllers receive their own parameters only.
        $request->route()->forgetParameter('locale');

        if ($request->cookie(self::COOKIE) !== $locale->value) {
            Cookie::queue(self::COOKIE, $locale->value, 60 * 24 * 365);
        }

        return $next($request);
    }
}
