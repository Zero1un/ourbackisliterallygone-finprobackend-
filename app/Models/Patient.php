<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientFile extends Model
{
    // Arahkan ke tabel files
    protected $table = 'files';

    // Sesuaikan dengan nama kolom yang ada di phpMyAdmin
    protected $fillable = [
        'fileable_type', 
        'fileable_id', 
        'file_path', 
        'original_name', 
        'mime_type', 
        'size', 
        'uploaded_by'
    ];
}