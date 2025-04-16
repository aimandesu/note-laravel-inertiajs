<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackGuestActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('guest_data.user_id')) {
            // Update last activity timestamp for the guest user
            User::where('id',  session('guest_data.user_id'))
                ->update(['last_activity' => now()]);
        }
        
        return $next($request);
    }
}
