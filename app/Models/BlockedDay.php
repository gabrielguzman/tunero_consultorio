<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'full_day', 'start_time', 'end_time', 'reason', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];
}