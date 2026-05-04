<?php

namespace App\Filament\Pages;

use App\Models\Area;
use App\Models\Need;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class GisMap extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'خريطة GIS';
    protected static ?string $title           = 'خريطة المناطق والاحتياجات';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.pages.gis-map';

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }

    public function getAreasData(): array
    {
        return Area::withCount([
            'needs',
            'needs as pending_needs_count' => fn ($q) => $q->where('status', 'pending'),
            'needs as delivered_needs_count' => fn ($q) => $q->where('status', 'delivered'),
        ])->get()->map(fn (Area $area) => [
            'id'              => $area->id,
            'name'            => $area->name,
            'lat'             => $area->latitude  ?? 23.8859,
            'lng'             => $area->longitude ?? 45.0792,
            'priority'        => $area->priority_level ?? 'medium',
            'status'          => $area->status ?? 'active',
            'needs_count'     => $area->needs_count,
            'pending'         => $area->pending_needs_count,
            'delivered'       => $area->delivered_needs_count,
        ])->toArray();
    }
}
