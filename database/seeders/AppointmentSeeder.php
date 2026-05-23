<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data dokter, pasien, dan jadwal pertama yang sudah dibuat seeder sebelumnya
        $patient = Patient::first();
        $doctor = Doctor::first();
        
        // Kita ambil ID jadwal dari tabel 'schedules' secara langsung agar aman
        $schedule = DB::table('schedules')->first();

        // Jaga-jaga jika seeder pendukungnya belum dieksekusi
        if (!$patient || !$doctor || !$schedule) {
            return;
        }

        // 2. Insert data janji temu secara permanen ke MySQL cocok 100% dengan migration-mu
        Appointment::create([
            'id'               => 1, // Kita kunci ID-nya bernilai 1 untuk testing Postman
            'patient_id'       => $patient->id,
            'doctor_id'        => $doctor->id,
            'schedule_id'      => $schedule->id,
            'appointment_date' => '2026-05-23',
            'status'           => 'completed', // Langsung diset 'completed' agar logis dibuatkan rekam medis
            'complaint'        => 'Pasien mengeluhkan pusing dan migrain akut setelah begadang.',
        ]);
    }
}