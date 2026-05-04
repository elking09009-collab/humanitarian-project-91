<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CsrCompany extends Model {
    protected $fillable = [
        'company_name','logo_url','contact_email','contact_phone','sector',
        'total_donated','matching_ratio','badge_level','description','website','status'
    ];
}
