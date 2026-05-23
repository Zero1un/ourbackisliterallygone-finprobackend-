<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil dokter pertama untuk dihubungkan ke jadwal praktik
        $doctor = Doctor::first();

        if (!$doctor) {
            return;
        }

        // Menyuntikkan data jadwal secara permanen ke MySQL
        DB::table('schedules')->insert([
            'id' => 1, // Kunci ID 1 agar sinkron
            'doctor_id' => $doctor->id,
            'day_of_week' => 'Monday', // Sesuaikan dengan nama kolom migration schedules kalian jika ada
            'start_time' => '09:00:00', // Sesuaikan jika strukturnya berbeda
            'end_time' => '12:00:00',   // Sesuaikan jika strukturnya berbeda
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}