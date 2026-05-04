<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Need extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasTranslations;

    public array $translatable = ['notes'];

    protected $fillable = [
        'area_id',
        'type',
        'quantity',
        'status',
        'notes',
        'organization_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope());

        static::creating(function (Need $need) {
            if (empty($need->organization_id) && Auth::check()) {
                $need->organization_id = Auth::user()?->organization_id;
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function comments()
    {
        return $this->hasMany(NeedComment::class);
    }
}
