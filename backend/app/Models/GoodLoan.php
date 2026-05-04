<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GoodLoan extends Model {
    protected $fillable = [
        'endowment_id','borrower_id','borrower_name',
        'amount','purpose','status','repaid_at'
    ];
}
