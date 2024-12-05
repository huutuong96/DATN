<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubdomainMiddleware
{
    public function handle($request, Closure $next)
    {
        // $host = $request->getHost(); // Lấy host từ request
        // $subdomain = explode('.', $host)[0]; // Tách subdomain từ host

        // // Xử lý logic tùy theo subdomain
        // if ($subdomain === 'api') {
        //     return redirect()->route('api.home');
        // }

        return $next($request);
    }
}
