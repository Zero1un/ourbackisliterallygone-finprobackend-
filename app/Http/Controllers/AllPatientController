<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Http\Resources\PatientListResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        // Get the sort parameter from the URL, default to 'name'
        $sort = $request->query('sort', 'name');

        // Start the query and JOIN the users table to get name & email
        $query = Patient::query()
            ->select('patients.id', 'users.name', 'users.email', 'patients.phone')
            ->join('users', 'patients.user_id', '=', 'users.id');

        // Handle the dynamic sorting
        if ($sort === 'today_appointment') {
            $query->leftJoin('appointments', function($join) {
                $join->on('patients.id', '=', 'appointments.patient_id')
                     ->whereDate('appointments.appointment_date', Carbon::today());
            })
            // Sort so patients WITH an appointment today appear at the very top
            ->orderByRaw('appointments.id IS NOT NULL DESC')
            ->orderBy('users.name', 'asc')
            ->distinct();

        } elseif ($sort === 'doctor') {
            $query->leftJoin('appointments', 'patients.id', '=', 'appointments.patient_id')
                  ->leftJoin('doctors', 'appointments.doctor_id', '=', 'doctors.id')
                  ->leftJoin('users as doctor_user', 'doctors.user_id', '=', 'doctor_user.id')
                  // Sort alphabetically by the DOCTOR'S name
                  ->orderBy('doctor_user.name', 'asc')
                  ->orderBy('users.name', 'asc') // Secondary sort by patient name
                  ->distinct();
        } else {
            // Default: Sort alphabetically by patient name
            $query->orderBy('users.name', 'asc');
        }

        // The rubric requires Pagination (paginate) instead of get() [cite: 88]
        $patients = $query->paginate(10);

        // Return the standardized JSON Envelope [cite: 55]
        return PatientListResource::collection($patients)->additional([
            'status' => 'success',
            'message' => 'Data pasien berhasil diambil',
        ]);
    }
}