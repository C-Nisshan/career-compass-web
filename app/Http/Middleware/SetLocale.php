<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('lang', Session::get('locale', config('app.locale')));
        Log::info('SetLocale Middleware', [
            'locale' => $locale,
            'query_lang' => $request->query('lang'),
            'session_locale' => Session::get('locale'),
            'config_locale' => config('app.locale'),
            'available_locales' => config('app.available_locales'),
            'session_id' => Session::getId(),
        ]);
        if (in_array($locale, config('app.available_locales', ['en', 'si', 'ta']))) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            Session::save();
        } else {
            $locale = config('app.fallback_locale', 'en');
            App::setLocale($locale);
            Session::put('locale', $locale);
            Session::save();
        }
        return $next($request);
    }
}