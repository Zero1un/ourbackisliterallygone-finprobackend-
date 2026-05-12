<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    use HasFactory;

    protected $fillable = [
        'fileable_type',
        'fileable_id',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by'
    ];

    public function fileable()
    {
        return $this->morphTo();
    }

    //identifikasi siapa klik tombol "upload" -> uploaded_by
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
