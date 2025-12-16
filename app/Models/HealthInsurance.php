<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthInsurance extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    // Scope para traer solo las activas
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}