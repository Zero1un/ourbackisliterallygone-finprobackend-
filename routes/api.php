<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\AppointmentController;
use App\Http\Controllers\API\v1\MedicalRecordController;

Route::middleware('force.json')->group(function () {

    // ==========================================
    // Public Routes (Bisa diakses tanpa login)
    // ==========================================
    Route::post('/auth/register', [\App\Http\Controllers\AuthPatientRegisterController::class, 'register']);
    Route::post('/auth/login', [\App\Http\Controllers\AuthLoginLogoutController::class, 'login']);

    // ==========================================
    // Protected Routes (Wajib Login / Sanctum)
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::post('/auth/logout', [\App\Http\Controllers\AuthLoginLogoutController::class, 'logout']);
        
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Admin Only Endpoints
        Route::post('/auth/register-doctor', [\App\Http\Controllers\AuthDoctorRegisterController::class, 'registerDoctor'])
            ->middleware('role:admin');

        // Doctor Only Endpoints (Fitur Rekam Medis Kamu)
        Route::middleware('role:doctor')->group(function () {
            Route::get('/medical-records', [MedicalRecordController::class, 'index']);
            Route::post('/medical-records', [MedicalRecordController::class, 'store']);
        });

        // API Version 1 Endpoints (Fitur Janji Temu Temanmu)
        Route::prefix('v1')->group(function () {
            
            // Patients only
            Route::post('/appointments', [AppointmentController::class, 'store'])
                ->middleware('role:patient');

            // Admins, Doctors, or Patients
            Route::get('/appointments/{id}', [AppointmentController::class, 'show'])
                ->middleware('role:admin,doctor,patient');

            // Admins or Doctors only
            Route::put('/appointments/{id}', [AppointmentController::class, 'update'])
                ->middleware('role:admin,doctor');

            // Admins, Doctors, or Patients
            Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])
                ->middleware('role:admin,doctor,patient');
        });

        Route::get('/cek-janji', function () {
            dd(\App\Models\Appointment::all()->toArray());
        });
    });
});