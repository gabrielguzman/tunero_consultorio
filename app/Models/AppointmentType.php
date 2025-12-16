<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'duration_minutes', 'price', 'color', 'is_active'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}