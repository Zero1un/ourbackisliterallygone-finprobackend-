<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware('force.json')->group(function () {

    // Public routes -> for patient / general user
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes -> must login 
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Admin only routes -> admin token
        Route::post('/auth/register-doctor', [AuthController::class, 'registerDoctor'])->middleware('role:admin');
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        //doctor only routes -> doctor token


        // Example of role and email verified middleware usage (commented out for reference)
        // Route::get('/admin/dashboard', [AdminController::class, 'index'])->middleware(['role:admin', 'email.verified']);
        // Route::get('/doctor/patients', [DoctorController::class, 'patients'])->middleware(['role:doctor', 'email.verified']);
    });
});
