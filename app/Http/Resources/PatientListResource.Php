<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,   // Coming from the users table JOIN
            'email' => $this->email, // Coming from the users table JOIN
            'phone' => $this->phone, // Coming from the patients table
        ];
    }
}