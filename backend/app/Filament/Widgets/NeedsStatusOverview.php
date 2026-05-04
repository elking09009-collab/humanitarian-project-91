<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Need;

class NeedsStatusOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            Stat::make('معلقة', Need::where('status', 'pending')->count())
                ->color('warning'),
            Stat::make('تم إيصالها', Need::where('status', 'delivered')->count())
                ->color('success'),
        ];
    }
}
