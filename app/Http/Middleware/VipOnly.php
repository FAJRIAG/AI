<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VipOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || !$user->is_vip) {
            // kalau bukan VIP, lempar balik ke public + pesan
            return redirect()->route('public.chat')->with('error', 'VIP only area.');
        }
        return $next($request);
    }
}
