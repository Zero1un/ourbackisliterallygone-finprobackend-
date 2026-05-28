<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\AppointmentController;
use App\Http\Controllers\API\v1\MedicalRecordController;
use App\Http\Controllers\API\PatientListController;
use App\Http\Controllers\API\v1\FileController;
use App\Http\Controllers\API\DetailPatientController;
use App\Http\Controllers\API\ExportReportController;

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

        // 2. Admin, Dokter, Pasien (Baca spesifik 1 rekam medis) 
        Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show'])
            ->middleware('role:admin,doctor,patient');

        Route::post('/files/upload-avatar', [FileController::class, 'uploadAvatar'])
            ->middleware('role:doctor,patient,admin'); 

        // 2. Hanya Boleh Diakses Oleh Doctor (atau sesuaikan dengan kebutuhanmu kemarin)
        // 2. Kunci khusus untuk DOCTOR saja
        Route::post('/files/upload-report', [FileController::class, 'uploadReport'])
            ->middleware('role:doctor,admin');
        
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

        // ==========================================
        // Fitur List Data Pasien (ListPatient v1)
        // ==========================================
        Route::prefix('v1')->group(function () {
  
        // Group protected by Sanctum and your custom Admin Role Middleware
        Route::middleware(['auth:sanctum', 'role:admin'])->group(function () 
            Route::get('/patients', [PatientListController::class, 'index']);
      
            }); 
        });
        // ==========================================
        // Fitur Detail Data Pasien (DetailPatient v1)
        // ==========================================
        Route::prefix('v1')->group(function () {
  
  Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::get('/patients/{id}', [PatientListController::class, 'show']);
    Route::put('/patients/{id}', [PatientListController::class, 'update']);
    
  });
  Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    
    Route::get('/patients', [PatientListController::class, 'index']);   
  });
});
    

            });
        });

        // ==========================================
        // Fitur Export report
        // ==========================================
        Route::prefix('v1')->group(function () {
            Route::middleware(['role:admin'])->group(function () {
                Route::get('/reports/export', [ExportReportController::class, 'export']);
            });
        });
});