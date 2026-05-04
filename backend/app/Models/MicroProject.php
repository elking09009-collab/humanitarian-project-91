<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MicroProject extends Model
{
    protected $fillable = ['beneficiary_id','area_id','name','description','category','target_amount','funded_amount','status','image_url'];
    public function beneficiary() { return $this->belongsTo(User::class, 'beneficiary_id'); }
    public function area()        { return $this->belongsTo(Area::class); }
}
