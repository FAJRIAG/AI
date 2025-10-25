<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LimitPublicMessages
{
    public function handle(Request $request, Closure $next)
    {
        // VIP (login & is_vip=true) bebas limit
        if ($request->user() && ($request->user()->is_vip ?? false)) {
            return $next($request);
        }

        // Limit per IP per hari
        $limit = (int) (config('app.public_daily_limit') ?? env('PUBLIC_DAILY_LIMIT', 10));

        $day   = now()->toDateString();
        $ip    = $request->ip();
        $key   = "pub_limit:{$day}:ip:{$ip}";

        $used = (int) Cache::get($key, 0);
        if ($used >= $limit) {
            return response()->json([
                'message'   => 'Batas harian non-VIP tercapai untuk IP ini.',
                'limit'     => $limit,
                'remaining' => 0,
            ], 429);
        }

        // increment & set expired end of day
        Cache::put($key, $used + 1, now()->endOfDay());

        $response = $next($request);
        return $response->header('X-Public-Limit', (string)$limit)
                        ->header('X-Public-Used', (string)($used + 1))
                        ->header('X-Public-Remaining', (string)max(0, $limit - ($used + 1)));
    }
}
