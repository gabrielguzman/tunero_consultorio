<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientFile extends Model
{
    protected $fillable = [
        'patient_id',
        'file_path',
        'original_name',
        'file_type',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
