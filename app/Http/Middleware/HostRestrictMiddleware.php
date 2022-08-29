<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HostRestrictMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->allowDomainFrom($request->getHost()) == false) {
            return response('', 400);
        }

        return $next($request);
    }

    private function allowDomainFrom(string $host): bool
    {
        foreach ([
            'localhost',
            '.vodafone.com',
            '.vodafone.biz',
        ] as $allowedDomain) {
            if (substr_compare($host, $allowedDomain, strlen($host) - strlen($allowedDomain), strlen($allowedDomain)) === 0) {
                return true;
            }
        }

        return false;
    }
}
