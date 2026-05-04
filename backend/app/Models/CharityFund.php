<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CharityFund extends Model
{
    protected $fillable = ['creator_id','name','description','project_type','goal_amount','current_amount','status','invite_code'];
    public function creator()       { return $this->belongsTo(User::class, 'creator_id'); }
    public function contributions() { return $this->hasMany(FundContribution::class, 'fund_id'); }
}
