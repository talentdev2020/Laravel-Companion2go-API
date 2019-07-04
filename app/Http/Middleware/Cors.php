<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class Cors
{

    const HEADERS = 'DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range,Authorization';
    const METHODS = 'GET,POST,OPTIONS,PUT,DELETE';

    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ($request->isMethod('OPTIONS')) {
            return response('', 200, [
                'Access-Control-Allow-Origin' => $request->header('Origin'),
                'Access-Control-Allow-Methods' => self::METHODS,
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Headers' => self::HEADERS
            ]);
        }

        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', $request->header('Origin'));
        $response->header('Access-Control-Allow-Methods',self::METHODS);
        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->header('Access-Control-Allow-Headers', self::HEADERS);
        return $response;
    }
}
