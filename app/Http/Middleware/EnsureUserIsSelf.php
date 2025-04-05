<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSelf
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeUser = $request->route('user');

        // Non autenticato → redirect a login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Autenticato ma non è se stesso → redirect a home
        if ($routeUser && Auth::id() !== $routeUser->id) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
