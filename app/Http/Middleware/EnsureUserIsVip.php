<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVip
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->is_vip) {
            return redirect()->route('public.chat')->with('error', 'VIP only area');
        }
        return $next($request);
    }
}
