<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Akun Auth Pasien di tabel 'users'
        $userPatient = User::create([
            'name' => 'Kiyo Hartono',
            'email' => 'kiyo@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
        ]);

        // 2. Buat Profil Pasien di tabel 'patients' dengan menyertakan date_of_birth
        Patient::create([
            'user_id' => $userPatient->id,
            'phone' => '089876543210',
            'address' => 'Malang',
            'date_of_birth' => '2000-05-23', // <-- Tambahkan baris ini (format: YYYY-MM-DD)
        ]);
    }
}