<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Area;

class AreasOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('عدد المناطق', Area::count())
                ->icon('heroicon-o-map'),
        ];
    }
}
