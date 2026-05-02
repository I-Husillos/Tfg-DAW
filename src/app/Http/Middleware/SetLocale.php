<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Auth::check()
            ? (Auth::user()->profile?->language ?? config('app.locale'))
            : config('app.locale');

        App::setLocale($locale);

        return $next($request);
    }
}
