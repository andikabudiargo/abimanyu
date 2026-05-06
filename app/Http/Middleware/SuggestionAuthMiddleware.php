<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class SuggestionAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('suggestion_user_id')) {
            return redirect()->route('suggestion.login')
                ->withErrors([
                    'email' => 'Silakan login terlebih dahulu.'
                ]);
        }

        return $next($request);
    }
}