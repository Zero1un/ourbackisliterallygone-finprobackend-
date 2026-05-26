<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportReportController extends Controller{
  
    public function export(Request $request){
    $format = $request->query('format', 'pdf');

    $reports = DB::table('medical_records')
      ->select(
        'medical_records.id',
        'appointments.appointment_date as date',
        'patient_user.name as patient_name',
        'doctor_user.name as doctor_name',
        'medical_records.diagnosis',
        'medical_records.prescription'
      )
      ->join('appointments', 'medical_records.appointment_id', '=', 'appointments.id')
      ->join('patients', 'appointments.patient_id', '=', 'patients.id')
      ->join('users as patient_user', 'patients.user_id', '=', 'patient_user.id')
      ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
      ->join('users as doctor_user', 'doctors.user_id', '=', 'doctor_user.id')
      ->orderBy('appointments.appointment_date', 'desc')
      ->get();

    // 2. Handle CSV Export
    if ($format === 'csv') {
      $headers = [
        'Content-type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename=laporan_rekam_medis_' . Carbon::now()->format('Ymd') . '.csv',
      ];

      $callback = function() use ($reports) {
        $file = fopen('php://output', 'w');
        
        // Write the CSV Headers
        fputcsv($file, ['ID', 'Tanggal', 'Nama Pasien', 'Nama Dokter', 'Diagnosis', 'Resep']);

        // Write the Data
        foreach ($reports as $row) {
          fputcsv($file, [
            $row->id,
            $row->date,
            $row->patient_name,
            $row->doctor_name,
            $row->diagnosis,
            $row->prescription
          ]);
        }
        fclose($file);
      };

      return response()->stream($callback, 200, $headers);
    }

    // 3. Handle PDF Export
    if ($format === 'pdf') {
      // Pass the $reports data to a Blade HTML template
      $pdf = Pdf::loadView('reports.medical-records', ['reports' => $reports]);
      
      return $pdf->download('laporan_rekam_medis_' . Carbon::now()->format('Ymd') . '.pdf');
    }

    return response()->json(['status' => 'error', 'message' => 'Format tidak valid'], 400);
  }
}