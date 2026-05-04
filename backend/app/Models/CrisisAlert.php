<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrisisAlert extends Model {
    protected $fillable = [
        'title','description','area_id','area_name','severity',
        'type','needed_items','needed_amount','current_amount','status','expires_at'
    ];
    protected $casts = ['expires_at' => 'datetime'];
}
