<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Mengunggah dokumen atau foto pasien ke storage
     */
    public function upload(Request $request)
    {
        // 1. Validasi input file (Maksimal 2MB, format gambar atau dokumen medis umum)
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,txt|max:2048',
        ]);

        // 2. Cek apakah ada file yang dikirim
        if ($request->file('file')) {
            $file = $request->file('file');
            
            // Buat nama file unik agar tidak saling menimpa (contoh: 1716482391_nama_asli.pdf)
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Simpan file ke folder 'public/patient_files'
            $filePath = $file->storeAs('patient_files', $fileName, 'public');

            // Ambil URL publiknya agar bisa diakses/diunduh nantinya
            $fileUrl = asset('storage/' . $filePath);

            return response()->json([
                'status'  => true,
                'message' => 'Berkas berhasil diunggah dengan sukses.',
                'data'    => [
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_url'  => $fileUrl,
                    'mime_type' => $file->getClientMimeType()
                ]
            ], 201);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Gagal mengunggah berkas. File tidak ditemukan.'
        ], 400);
    }
}