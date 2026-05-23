<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,   // Punya Yosef yang sudah aman
            DoctorSeeder::class,  // Dokter otomatis masuk ke seeder dokter
            PatientSeeder::class, // Pasien masuk ke seeder pasien
            ScheduleSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}