<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * robots.txt is generated so it can point at the sitemap of the current host
 * and keep non-production environments out of search engines.
 */
final class RobotsController
{
    public function __invoke(): Response
    {
        $rules = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', 'Disallow: /livewire']
            : ['User-agent: *', 'Disallow: /'];

        $body = implode("\n", [...$rules, '', 'Sitemap: '.route('sitemap')])."\n";

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
