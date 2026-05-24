<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    // ==========================================
    // 1. Upload Foto Profil (Avatar)
    // ==========================================
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg|max:10240', // Maksimal 2MB
        ]);

        if ($request->file('file')) {
            $file = $request->file('file');
            
            // Ambil informasi file asli
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $size = $file->getSize();
            $userId = Auth::id(); // Mengambil ID user yang sedang login

            // Buat nama unik dan simpan file fisik ke disk public
            // Menyimpan ke folder avatars/tahun/bulan/tanggal/user_id
            $folderPath = 'avatars/' . date('Y/m/d') . '/' . $userId;
            $fileName = time() . '_' . str_replace(' ', '_', $originalName);
            $path = $file->storeAs($folderPath, $fileName, 'public');

            // Simpan data text ke Database (Asumsi menggunakan struktur tabel polymorph seperti contohmu)
            $newFile = File::create([
                'fileable_type' => 'App\Models\User', // Sesuai kebutuhan model target avatar
                'fileable_id'   => $userId,
                'file_path'     => $path,
                'original_name' => $originalName,
                'mime_type'     => $mimeType,
                'size'          => $size,
                'uploaded_by'   => $userId,
            ]);

            // Kembalikan respon JSON lengkap sesuai keinginanmu
            return response()->json([
                'message' => 'Avatar berhasil diunggah',
                'data' => [
                    'fileable_type' => $newFile->fileable_type,
                    'fileable_id'   => $newFile->fileable_id,
                    'file_path'     => $newFile->file_path,
                    'original_name' => $newFile->original_name,
                    'mime_type'     => $newFile->mime_type,
                    'size'          => $newFile->size,
                    'uploaded_by'   => $newFile->uploaded_by,
                    'updated_at'    => $newFile->updated_at,
                    'created_at'    => $newFile->created_at,
                    'id'            => $newFile->id,
                ]
            ], 200);
        }
    }

    // ==========================================
    // 2. Upload PDF Laporan Pasien (Report)
    // ==========================================
    public function uploadReport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:10240', // Maksimal 10MB PDF
        ]);

        if ($request->file('file')) {
            $file = $request->file('file');
            
            // Ambil informasi file asli
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $size = $file->getSize();
            $userId = Auth::id();

            // Buat nama unik dan simpan file fisik ke disk public
            $folderPath = 'patient_files/' . date('Y/m/d') . '/' . $userId;
            $fileName = time() . '_' . str_replace(' ', '_', $originalName);
            $path = $file->storeAs($folderPath, $fileName, 'public');

            // Simpan data text ke Database
            $newFile = File::create([
                'fileable_type' => 'App\Models\MedicalRecord', // Mengarah ke model MedicalRecord
                'fileable_id'   => $request->input('medical_record_id', $userId), // Mengambil id rekam medis dari input form-data, jika tidak ada default ke userId
                'file_path'     => $path,
                'original_name' => $originalName,
                'mime_type'     => $mimeType,
                'size'          => $size,
                'uploaded_by'   => $userId,
            ]);

            // Kembalikan respon JSON lengkap
            return response()->json([
                'message' => 'Dokumen berhasil diunggah',
                'data' => [
                    'fileable_type' => $newFile->fileable_type,
                    'fileable_id'   => $newFile->fileable_id,
                    'file_path'     => $newFile->file_path,
                    'original_name' => $newFile->original_name,
                    'mime_type'     => $newFile->mime_type,
                    'size'          => $newFile->size,
                    'uploaded_by'   => $newFile->uploaded_by,
                    'updated_at'    => $newFile->updated_at,
                    'created_at'    => $newFile->created_at,
                    'id'            => $newFile->id,
                ]
            ], 200);
        }
    }

    public function show($id)
    {
        $patientFile = File::find($id);

        if (!$patientFile) {
            return response()->json([
                'status'  => false,
                'message' => 'Berkas tidak ditemukan di database.'
            ], 404);
        }

        $user = auth()->user();
        if ($patientFile->uploaded_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Anda tidak memiliki akses ke berkas ini'], 403);
        }

        if (!Storage::disk('public')->exists($patientFile->file_path)) {
            return response()->json([
                'status'  => false,
                'message' => 'Fisik berkas tidak ditemukan di server.'
            ], 404);
        }

        $fullPath = storage_path('app/public/' . $patientFile->file_path);
        
        return response()->download($fullPath, $patientFile->original_name, [
            'Content-Type' => $patientFile->mime_type
        ]);
    }

    public function destroy($id)
    {
        $patientFile = File::find($id);

        if (!$patientFile) {
            return response()->json([
                'status'  => false,
                'message' => 'Data berkas tidak ditemukan.'
            ], 404);
        }

        $user = auth()->user();
        if ($patientFile->uploaded_by !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Anda tidak memiliki wewenang untuk menghapus berkas ini'], 403);
        }

        if (Storage::disk('public')->exists($patientFile->file_path)) {
            Storage::disk('public')->delete($patientFile->file_path);
        }

        $patientFile->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Berkas berhasil dihapus secara permanen dari server.'
        ], 200);
    }
}