<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrganisationController;
 use App\Http\Controllers\Api\ReportController;
 use App\Http\Controllers\Api\DashboardController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    Route::post('/logout', [AuthController::class, 'logout']);

    // Users routes - Note: order matters!
    Route::get('/users/selected', [UserController::class, 'selected']); // Must come before /users/{id}
    Route::get('/users', [UserController::class, 'index']);          // Get all users
    Route::get('/users/{id}', [UserController::class, 'show']);      // Get a specific user
    Route::post('/users', [UserController::class, 'store']);         // Create a new user    
    Route::put('/users/{id}', [UserController::class, 'update']);    // Update a specific user
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Delete a specific user

    // Organisation routes
    Route::get('/organisations', [OrganisationController::class, 'index']);          
    Route::get('/organisations/{id}', [OrganisationController::class, 'show']);
    Route::post('/organisations', [OrganisationController::class, 'store']);         
    Route::put('/organisations/{id}', [OrganisationController::class, 'update']);    
    Route::delete('/organisations/{id}', [OrganisationController::class, 'destroy']);

    // Report generation routes
    Route::post('/reports/generate', [ReportController::class, 'generate']);
});

// Authentication routes
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

// CSRF protection route for Sanctum
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['message' => 'CSRF cookie set']);
});