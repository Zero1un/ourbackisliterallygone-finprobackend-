<?php

namespace App\Http\Controllers\API\v1;

use App\Models\MedicalRecord;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Menampilkan semua data rekam medis
     */
    public function index()
    {
        // Mengambil semua data rekam medis beserta relasi data appointment dan doctor agar informatif
        $records = MedicalRecord::with(['appointment', 'doctor.user'])->get();

        return response()->json([
            'status' => true,
            'message' => 'List Data Rekam Medis Berhasil Diambil',
            'data'    => $records
        ], 200);
    }

    /**
     * Menyimpan data rekam medis baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi inputan Postman
        $request->validate([
            'appointment_id' => 'required|integer|exists:appointments,id',
            'diagnosis'      => 'required|string',
            'prescription'   => 'required|string',
            'notes'          => 'nullable|string',
        ]);

        // 2. Ambil User yang sedang login
        $user = $request->user(); 

        // 3. Cari ID Dokter asli dari tabel 'doctors' berdasarkan user_id yang sedang login
        $doctorProfile = $user->doctor;

        if (!$doctorProfile) {
            return response()->json([
                'status'  => false,
                'message' => 'Profil medis dokter tidak ditemukan atau Anda bukan dokter resmi.'
            ], 403);
        }

        // 4. Simpan data ke database dengan doctor_id yang BENAR (bukan user_id)
        $medicalRecord = MedicalRecord::create([
            'appointment_id' => $request->appointment_id,
            'doctor_id'      => $doctorProfile->id, // Menggunakan ID dari tabel doctors
            'diagnosis'      => $request->diagnosis,
            'prescription'   => $request->prescription,
            'notes'          => $request->notes,
        ]);

        // 5. Response Envelope yang seragam
        return response()->json([
            'status'  => true,
            'message' => 'Rekam medis berhasil ditambahkan oleh Dr. ' . $user->name,
            'data'    => $medicalRecord
        ], 201);
    }

    public function show($id)
    {
        // Cari rekam medis berdasarkan ID, sekalian load relasi appointment dan doctor
        $medicalRecord = MedicalRecord::with(['appointment', 'doctor.user'])->find($id);

        // Jika data tidak ditemukan, kembalikan error 404
        if (!$medicalRecord) {
            return response()->json([
                'status'  => false,
                'message' => 'Data rekam medis tidak ditemukan.'
            ], 404);
        }

        // Jika ketemu, kembalikan datanya dengan status 200 OK
        return response()->json([
            'status'  => true,
            'message' => 'Detail rekam medis berhasil diambil',
            'data'    => $medicalRecord
        ], 200);
    }
}