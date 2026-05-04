<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SkillDonation extends Model
{
    protected $fillable = ['donor_id','need_id','skill_type','skill_title','description','hours_offered','contact_info','status'];
    public function donor() { return $this->belongsTo(User::class, 'donor_id'); }
    public function need()  { return $this->belongsTo(Need::class); }
}
