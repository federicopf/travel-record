<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IpAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $userIp = $request->ip(); 

        if (Cache::has("allowed_ip_{$userIp}")) {
            return $next($request);
        }

        dd('aa');
        return redirect('/login');
    }
}
