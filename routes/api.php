<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\AppointmentController;
use App\Http\Controllers\API\v1\MedicalRecordController;
use App\Http\Controllers\API\v1\FileController;

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

        // ==========================================
        // Fitur Rekam Medis (Medical Records)
        // ==========================================
        
        // 1. Doctor Only (Lihat semua & Tambah baru)
        Route::middleware('role:doctor')->group(function () {
            Route::get('/medical-records', [MedicalRecordController::class, 'index']);
            Route::post('/medical-records', [MedicalRecordController::class, 'store']);
        });

        // 2. Admin, Dokter, Pasien (Baca spesifik 1 rekam medis) - INI ENDPOINT BARUMU
        Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show'])
            ->middleware('role:admin,doctor,patient');

        Route::post('/files/upload', [FileController::class, 'upload']);

        Route::post('/files/upload', [FileController::class, 'upload']);
        Route::get('/files/{id}', [FileController::class, 'show']);
        Route::delete('/files/{id}', [FileController::class, 'destroy']);

        // ==========================================
        // Fitur Janji Temu (Appointments v1)
        // ==========================================
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