<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\v1\AppointmentController;

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

        /*

        |--------------------------------------------------------------------------
        | API Version 1 Endpoints (Mandatory Project Requirement)
        |--------------------------------------------------------------------------
        */
        Route::prefix('v1')->group(function () {
            
            // Patients only: Can create an appointment booking
            Route::post('/appointments', [AppointmentController::class, 'store'])
                ->middleware('role:patient');

            // Admins, Doctors, or Patients: Can view single appointment details
            Route::get('/appointments/{id}', [AppointmentController::class, 'show'])
                ->middleware('role:admin,doctor,patient');

            // Admins or Doctors only: Can change or verify appointment statuses
            Route::put('/appointments/{id}', [AppointmentController::class, 'update'])
                ->middleware('role:admin,doctor');

            // Admins, Doctors, or Patients: Can cancel/remove an appointment record
            Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])
                ->middleware('role:admin,doctor,patient');
                
        });
    });
});
