<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LegacyGiving extends Model {
    protected $fillable = [
        'user_id','full_name','national_id','amount','percentage',
        'trigger_event','beneficiary_category','notes','legal_document_url','status'
    ];
}
