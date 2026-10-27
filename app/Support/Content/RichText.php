<?php

namespace App\Support\Content;

use Illuminate\Support\HtmlString;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Sanitises rich text written in the CMS before it is rendered publicly.
 *
 * Editors are trusted, but their HTML is still filtered to a safe subset so
 * that pasted markup, inline styles or scripts never reach visitors.
 */
final class RichText
{
    private static ?HtmlSanitizer $sanitizer = null;

    public static function render(?string $html): HtmlString
    {
        return new HtmlString(self::sanitizer()->sanitize((string) $html));
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
                ->allowMediaSchemes(['https', 'http'])
                ->allowAttribute('dir', '*')
                ->allowAttribute('lang', '*')
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                ->withMaxInputLength(200_000),
        );
    }
}
