<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{  

    public function signup(SignupRequest $request)
    {
        $data = $request->validated();

        // Force new users to always be EMPLOYEE
        $data['role'] = 'EMPLOYEE';

        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            //'password' => bcrypt($data['password']) 
            'password' => Hash::make($data['password']),
            'role' => $data['role'], // Ensures EMPLOYEE role is enforced
        ]);

        Auth::login($user);

        // $response = response()->json(['user' => $user]);
        // Log::info('Response Headers:', $response->headers->all());
        // return $response;
        
        return response()->json(['user' => $user]);
    }

    public function login(LoginRequest $request)
    {
        Log::info('Incoming Headers:', $request->headers->all());
        Log::info('Incoming Cookies:', $request->cookies->all());
        Log::info('CSRF Token:', ['token' => $request->header('X-XSRF-TOKEN')]);
        Log::info('CSRF Cookie:', ['cookie' => $request->cookie('XSRF-TOKEN')]);

        $credentials = [
            'email' => $request->validated()['email'],
            'password' => $request->validated()['password'],
        ];

        if (!Auth::attempt($credentials)) {
            Log::warning('Failed login attempt:', ['email' => $credentials['email']]);

            abort(401, 'Invalid email or password'); // Let Laravel handle this through Handler.php
        }        

        /** @var User $user */
        $user = Auth::user();
        Log::info('User Logged In:', ['user' => $user]);

        // Store last_activity timestamp on login
        Session::put('last_activity', now());
        Log::info( "Initialized last_activity value from Login " . Session::get('last_activity'));        

        // Check if response includes the cookie
        $response = response()->json(['user' => $user]);
        Log::info('Response Headers:', $response->headers->all());
        return $response;

        //return response()->json(['user' => $user]);
    }

    public function logout(Request $request)
    {
        /** @var User $user */
        // $user = $request->user();
        // $user->currentAccessToken()->delete();
        // return response('', 204);

        Auth::guard('web')->logout();

        // Invalidate session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear authentication cookies
        return response()->json(['message' => 'Logged out'])->withCookie(
            cookie()->forget('XSRF-TOKEN')
        )->withCookie(
            cookie()->forget('laravel_session')
        );
    }
}
