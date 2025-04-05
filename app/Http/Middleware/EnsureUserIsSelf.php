<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSelf
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeUser = $request->route('user');

        if (auth()->check() && $routeUser && auth()->id() != $routeUser->id) {
            abort(403, 'Non puoi accedere a questo profilo.');
        }

        return $next($request);
    }
}
