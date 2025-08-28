<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;

class InactiveSessionLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('InactiveSessionLogout middleware triggered');
        
        if (Auth::check()) {
            if (!Session::has('last_activity')) {
                Log::info('Session last_activity key not found. Setting initial value.');
                Session::put('last_activity', now());
            } 

            $inactiveTime = now()->diffInMinutes(Session::get('last_activity'));
            Log::info("Inactive Time: $inactiveTime minutes");
            Log::info("Defined env Session Lifetime: " . config('session.lifetime') . " minutes");
            
            //Expire session if inactive time reaches the session lifetime
            if ($inactiveTime !== null && $inactiveTime >= (int) config('session.lifetime')) {
                Log::info("*** Expire Session due to inactivity after {$inactiveTime} minutes. ***");

                //Auth::logout();
                Session::invalidate();
                return response()->json(['message' => 'Session expired.'], 401);
            }

            // *** Refresh session activity timestamp only if the session is still valid and
            // Only update `last_activity` if NOT an Automatic Request of Periodic Session Refresh via ('api/user') ***
            if (!$request->is('api/user')) {
                Session::put('last_activity', now());
                Log::info("Session refreshed upon user activity, last_activity updated.");
            }
        }

        //---------------------------------------------

        // if (Auth::check()) {
        //     if (!Session::has('last_activity')) {
        //         \Log::info('Session last_activity key not found. Setting initial value.');
        //         Session::put('last_activity', now());
        //     }
    
        //     $inactiveTime = now()->diffInMinutes(Session::get('last_activity'));
    
        //     if ($inactiveTime >= config('session.lifetime')) {
        //         \Log::info("User logged out due to inactivity after {$inactiveTime} minutes.");
    
        //         Auth::logout();
        //         Session::invalidate();
    
        //         return response()->json(['message' => 'Session expired.'], 401);
        //     }
        // }

        // Session::put('last_activity', now());
        // Log::info("Session refreshed, last_activity updated.");
    
        return $next($request);
    }
}
