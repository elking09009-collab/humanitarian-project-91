<?php

namespace App\Filament\Widgets;

use App\Services\NeedsPredictorService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PredictionsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $predictions = app(NeedsPredictorService::class)->predictNextMonth();

        $top = $predictions[0] ?? null;

        $positiveTrends = collect($predictions)->where('trend_slope', '>', 0)->count();

        return [
            Stat::make('أعلى توقع للشهر القادم', $top ? (string) $top['predicted_next_month'] : '0')
                ->description($top ? ('نوع: ' . $top['type'] . ' | منطقة: ' . $top['area_id']) : 'لا توجد بيانات كافية')
                ->color('warning'),

            Stat::make('اتجاهات تصاعدية', (string) $positiveTrends)
                ->description('عدد السلاسل ذات نمو متوقع موجب')
                ->color('success'),

            Stat::make('إجمالي السلاسل المحللة', (string) count($predictions))
                ->description('حسب (المنطقة + نوع الاحتياج)')
                ->color('info'),
        ];
    }
}
