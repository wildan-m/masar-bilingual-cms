<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Silently discards form submissions that filled in the hidden honeypot
 * field. Bots get the normal success redirect, so they learn nothing.
 */
final class BlockHoneypotSubmissions
{
    public const FIELD = 'website';

    public function handle(Request $request, Closure $next, string $redirectRoute): Response
    {
        if (filled($request->input(self::FIELD))) {
            Log::notice('Discarded a form submission that filled the honeypot field.', [
                'route' => $request->route()?->getName(),
                'ip' => $request->ip(),
            ]);

            return redirect()->route($redirectRoute);
        }

        return $next($request);
    }
}
