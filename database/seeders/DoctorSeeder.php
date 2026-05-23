<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Akun Auth Dokter di tabel 'users'
        $userDoctor = User::create([
            'name' => 'Rich Yosef',
            'email' => 'dokter@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        // 2. Buat Profil Medisnya di tabel 'doctors' menggunakan id di atas
        Doctor::create([
            'user_id' => $userDoctor->id,
            'specialization' => 'Cardiologist',
            'phone' => '081234567890',
            'photo' => null, // atau kasih string path jika ada
        ]);
    }
}