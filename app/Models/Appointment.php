<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'patient_id',
        'appointment_type_id',
        'start_time',
        'end_time',
        'status',
        'patient_notes',
        'doctor_notes',
        'is_overtime',
        'reminder_sent_at',
        'created_by',
        'cancelled_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'is_overtime' => 'boolean',
    ];

    // --- Relaciones ---
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // El padre/madre
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class); // El niño
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type_id');
    }
}