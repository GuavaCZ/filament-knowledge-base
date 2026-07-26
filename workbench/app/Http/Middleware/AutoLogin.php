<?php

namespace Workbench\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Workbench\App\Models\User;

/**
 * Autologin user into workbench for easier local testing.
 */
class AutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() && ($user = User::query()->first())) {
            Auth::login($user);
        }

        return $next($request);
    }
}
