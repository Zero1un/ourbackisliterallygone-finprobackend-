<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $user = $request->user();
        $file = $request->file('file');
        
        // STRUKTUR FOLDER: patient_files/tahun/bulan/tanggal/user_id/
        $folderPath = 'patient_files/' . date('Y/m/d') . '/' . $user->id;
        
        // Simpan file
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($folderPath, $fileName, 'public');

        // Simpan ke database
        $patientFile = File::create([
            'fileable_type' => 'App\Models\User',
            'fileable_id'   => $user->id,
            'file_path'     => $filePath, // path sekarang berisi struktur folder rapi
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'uploaded_by'   => $user->id,
        ]);

        return response()->json(['message' => 'File berhasil diunggah', 'data' => $patientFile], 201);
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
        
        // 🟢 Ubah file_name menjadi original_name
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