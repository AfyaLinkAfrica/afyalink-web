<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the interface language the visitor chose with the language switcher.
 * Only locales listed in config('afyalink.locales') are accepted; anything else is ignored.
 */
class SetLocale
{
    public const COOKIE = 'afyalink_locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie(self::COOKIE);

        if (is_string($locale) && array_key_exists($locale, config('afyalink.locales'))) {
            app()->setLocale($locale);
        }

        $response = $next($request);

        // Pages differ by this cookie, so a shared cache (a CDN or reverse proxy) must keep
        // one copy per language choice instead of serving one language to everybody.
        $response->headers->set('Vary', 'Cookie', false);

        return $response;
    }
}
