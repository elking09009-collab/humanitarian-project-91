<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwinFamily extends Model
{
    protected $fillable = [
        'family_head_name', 'phone', 'city', 'area',
        'members_count', 'needs', 'story', 'status', 'matched_supporter_id',
    ];

    protected $casts = [
        'needs' => 'array',
    ];

    public function supporter()
    {
        return $this->belongsTo(TwinSupporter::class, 'matched_supporter_id');
    }

    public function messages()
    {
        return $this->hasMany(TwinMessage::class, 'family_id');
    }
}
