<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name',
        'contact_phone',
        'contact_email',
        'start_hour',
        'end_hour',
        'work_weekends'
    ];
}
