<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model {
    protected $fillable = [
        'user_id','author_name','title','content',
        'city','category','replies_count','views_count'
    ];
}
