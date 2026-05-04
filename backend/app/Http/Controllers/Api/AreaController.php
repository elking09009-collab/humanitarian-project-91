<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;

class AreaController extends Controller
{
    public function index()
    {
        return Area::withoutGlobalScopes()->where('status', 'active')->get()->map(fn ($a) => [
            'id'             => $a->id,
            'name'           => $a->getTranslations('name'),
            'name_ar'        => $a->getTranslation('name', 'ar', false),
            'description'    => $a->getTranslations('description'),
            'latitude'       => $a->latitude,
            'longitude'      => $a->longitude,
            'priority_level' => $a->priority_level,
            'status'         => $a->status,
        ]);
    }

    public function show($id)
    {
        $a = Area::withoutGlobalScopes()->findOrFail($id);
        return [
            'id'             => $a->id,
            'name'           => $a->getTranslations('name'),
            'name_ar'        => $a->getTranslation('name', 'ar', false),
            'description'    => $a->getTranslations('description'),
            'latitude'       => $a->latitude,
            'longitude'      => $a->longitude,
            'priority_level' => $a->priority_level,
            'status'         => $a->status,
        ];
    }
}
