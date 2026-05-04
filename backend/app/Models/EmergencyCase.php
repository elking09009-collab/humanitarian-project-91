<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmergencyCase extends Model {
    protected $fillable = [
        'title','description','type','needed_amount','current_amount',
        'contact_info','area','deadline','status','verified_by','is_pinned'
    ];
    protected $casts = ['deadline' => 'datetime', 'is_pinned' => 'boolean'];
}
