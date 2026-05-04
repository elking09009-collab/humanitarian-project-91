<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerApplication extends Model
{
    protected $fillable = [
        'volunteer_name',
        'phone',
        'city',
        'age',
        'specialties',
        'notes',
        'status',
    ];
}