<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Translatable\HasTranslations;

class Area extends Model
{
    use HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'priority_level',
        'status',
        'description',
        'organization_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope());

        static::creating(function (Area $area) {
            if (empty($area->organization_id) && Auth::check()) {
                $area->organization_id = Auth::user()?->organization_id;
            }
        });
    }

    public function needs()
    {
        return $this->hasMany(Need::class);
    }
}
