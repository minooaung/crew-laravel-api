<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Symfony\Component\HttpFoundation\Response;

// class AdminMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle(Request $request, Closure $next): Response
//     {
//         // if (!Auth::check() || Auth::user()->role !== 'ADMIN') {
//         //     // If the user is not authenticated or not an admin, return a 403 Forbidden response
//         //     return response()->json(['error' => 'Forbidden'], 403);
//         // }

//         // Only checks for authentication; trusts policies to handle RBAC
//         if (!Auth::check()) {
//             return response()->json(['error' => 'Unauthenticated. Please log in again.'], 401);
//         }

//         return $next($request);
//     }
// }
