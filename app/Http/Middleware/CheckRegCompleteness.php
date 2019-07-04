<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRegCompleteness
{
    const REG_COMPLETED_STATE = 6;
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->progress < self::REG_COMPLETED_STATE)  {
            return response()->json(['error' => 'registration_not_completed', 'user' => Auth::user()], 202);
        }
        
        return $next($request);
    }
}
