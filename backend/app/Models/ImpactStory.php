<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ImpactStory extends Model {
    protected $fillable = [
        'title','content','audio_url','author_name','city',
        'category','likes_count','cover_image','is_published'
    ];
}
