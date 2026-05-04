<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VolunteerValidation extends Model
{
    protected $fillable = ['need_id','volunteer_id','field_notes','document_urls','status','admin_feedback'];
    protected $casts = ['document_urls' => 'array'];
    public function need()      { return $this->belongsTo(Need::class); }
    public function volunteer() { return $this->belongsTo(User::class, 'volunteer_id'); }
}
