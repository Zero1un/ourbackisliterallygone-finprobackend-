<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Http\Resources\PatientListResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DetailPatientController extends Controller
{
    //fungtion get a deailed patient data
    public function show($id, Request $request)
  {
    
    $patient = Patient::with(['user', 'appointments.doctor.user'])->find($id);

    if (!$patient) {
      return response()->json(['status' => 'error', 'message' => 'Patient not found'], 404);
    }

    $currentUser = $request->user();
    if ($currentUser->role !== 'admin' && $currentUser->id !== $patient->user_id) {
      return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
    }

    return response()->json([
      'status' => 'success',
      'message' => 'Detail data pasien berhasil diambil',
      'data' => [
        'id' => $patient->id,
        'name' => $patient->user->name,
        'email' => $patient->user->email,
        'phone' => $patient->phone,
        'address' => $patient->address,
        'date_of_birth' => $patient->date_of_birth->format('Y-m-d'),
        'photo' => $patient->photo,
        // Include their appointment history since it's a detail view!
        'appointments' => $patient->appointments->map(fn($appt) => [
          'id' => $appt->id,
          'date' => $appt->appointment_date->format('Y-m-d'),
          'status' => $appt->status,
          'doctor_name' => $appt->doctor->user->name,
          'complaint' => $appt->complaint
        ])
      ]
    ]);
  }

  public function update(Request $request, $id)
  {
    $patient = Patient::with('user')->find($id);

    if (!$patient) {
      return response()->json(['status' => 'error', 'message' => 'Patient not found'], 404);
    }

    $currentUser = $request->user();
    if ($currentUser->role !== 'admin' && $currentUser->id !== $patient->user_id) {
      return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 403);
    }

    $validated = $request->validate([
      'name' => 'sometimes|string|max:255',
      'phone' => 'sometimes|string|max:20',
      'address' => 'sometimes|string',
      'date_of_birth' => 'sometimes|date',
    ]);

    if (isset($validated['name'])) {
      $patient->user->update(['name' => $validated['name']]);
    }

    $patient->update($request->only(['phone', 'address', 'date_of_birth']));

    return response()->json([
      'status' => 'success',
      'message' => 'Data pasien berhasil diupdate',
      'data' => [
        'id' => $patient->id,
        'name' => $patient->user->name,
        'phone' => $patient->phone,
      ]
    ]);
  }

}