<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectTemporaryHost
{
    public function handle(Request $request, Closure $next): Response
    {
        $temporaryHost = 'tan-reindeer-584222.hostingersite.com';
        $publicHost = 'islamabadbullionexchange.com';
        $forwardedHost = strtolower((string) $request->headers->get('X-Forwarded-Host'));

        if (
            strtolower($request->getHost()) === $temporaryHost
            && ! in_array($forwardedHost, [$publicHost, 'www.'.$publicHost], true)
        ) {
            return redirect()->away('https://'.$publicHost.$request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
