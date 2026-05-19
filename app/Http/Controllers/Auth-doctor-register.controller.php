<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class AuthDoctorRegisterController extends Controller
{
    public function registerDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'specialization' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'doctor',
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialization' => $validated['specialization'],
                'phone' => $validated['phone'],
                'photo' => $validated['photo'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Doctor registration successful',
                'data' => [
                    'user' => $user,
                    'doctor' => $doctor,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}