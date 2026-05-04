<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FundContribution extends Model
{
    protected $fillable = ['fund_id','user_id','amount','note'];
    public function fund() { return $this->belongsTo(CharityFund::class, 'fund_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
