<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwinSupporter extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'city',
        'support_types', 'monthly_budget', 'bio', 'status', 'matched_family_id',
    ];

    protected $casts = [
        'support_types' => 'array',
    ];

    public function family()
    {
        return $this->belongsTo(TwinFamily::class, 'matched_family_id');
    }

    public function messages()
    {
        return $this->hasMany(TwinMessage::class, 'supporter_id');
    }
}
