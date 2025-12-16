<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'health_insurance_id',
        'affiliate_number',
        'name',
        'birth_date',
        'gender',
        'medical_alerts'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // --- Accesor: Edad automática ---
    // Uso: $patient->age_string (Ej: "2 años, 3 meses")
    public function getAgeStringAttribute(): string
    {
        return Carbon::parse($this->birth_date)->diffForHumans(null, true);
    }

    // --- Relaciones ---
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function healthInsurance(): BelongsTo
    {
        return $this->belongsTo(HealthInsurance::class);
    }
}