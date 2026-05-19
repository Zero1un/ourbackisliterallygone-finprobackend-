<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'BinusHospitalAdmin',
            'email' => 'b!Nus4dmin@binus.medic.id',
            'password' => Hash::make('BeelinguaIsFucked'),
            'role' => 'admin',
        ]);
    }
}
