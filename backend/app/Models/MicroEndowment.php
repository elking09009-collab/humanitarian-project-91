<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MicroEndowment extends Model {
    protected $fillable = [
        'name','description','type','goal_amount','current_amount',
        'return_rate','creator_id','beneficiary_category','status'
    ];
}
