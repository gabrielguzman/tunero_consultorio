<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // <--- Importante importar esto

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dni',
        'birth_date',
        'health_insurance_id',
        'affiliate_number',
        'medical_alerts'
    ];

    // Accessor para mostrar la edad bonita (ej: "5 años")
    public function getAgeStringAttribute()
    {
        return \Carbon\Carbon::parse($this->birth_date)->age . ' años';
    }

    // Relación: Un paciente pertenece a un Usuario (Padre/Madre)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Un paciente tiene una Obra Social
    public function healthInsurance(): BelongsTo
    {
        return $this->belongsTo(HealthInsurance::class);
    }

    // --- ESTA ES LA QUE FALTABA ---
    // Relación: Un paciente tiene muchos Turnos
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function files(){
        return $this->hasMany(PatientFile::class);
    }
}
