<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $table = 'business_settings';

    // ESTA ES LA LISTA DE "PERMITIDOS". Si falta uno aquí, no se guarda.
    protected $fillable = [
        'business_name',
        'contact_email',
        'contact_phone',
        'start_hour',         
        'end_hour',          
        'work_weekends',     
        'max_turnos_por_dia', // <--- IMPORTANTE
        'allow_overbooking'   // <--- IMPORTANTE
    ];
}