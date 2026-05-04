<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model {
    protected $fillable = [
        'type','title','description','quantity','condition',
        'donor_id','area_id','status','expiry_date','contact_info',
        'pharmacist_notes','image_url'
    ];
}
