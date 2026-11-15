<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    private const ONE_YEAR_IN_MINUTES = 525600;

    /**
     * Remember the chosen language in a cookie and go back to the page the visitor was on.
     * A code that is not an enabled locale is a 404 and sets nothing.
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('afyalink.locales')), 404);

        return redirect($this->returnPath($request->query('return')))
            ->withCookie(cookie(SetLocale::COOKIE, $locale, self::ONE_YEAR_IN_MINUTES, '/', null, null, true, false, 'lax'));
    }

    /**
     * Only a path on this site is accepted, so the switcher cannot be used as an open redirect.
     * Anything else, including "//host" and "/\host" forms, goes to the home page.
     */
    private function returnPath(mixed $path): string
    {
        if (is_string($path) && preg_match('#^/(?![/\x5c])\S*\z#', $path) === 1) {
            return $path;
        }

        return '/';
    }
}
