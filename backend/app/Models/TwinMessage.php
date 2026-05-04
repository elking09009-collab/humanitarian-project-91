<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwinMessage extends Model
{
    protected $fillable = [
        'supporter_id', 'family_id', 'sender_type', 'message', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
