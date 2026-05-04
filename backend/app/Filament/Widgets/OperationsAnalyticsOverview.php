<?php

namespace App\Filament\Widgets;

use App\Models\Area;
use App\Models\Need;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperationsAnalyticsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalNeeds = Need::count();
        $deliveredNeeds = Need::where('status', 'delivered')->count();
        $completionRate = $totalNeeds > 0 ? round(($deliveredNeeds / $totalNeeds) * 100, 1) : 0;

        $avgResponseHours = round(
            Need::where('status', 'delivered')
                ->selectRaw('AVG((julianday(updated_at) - julianday(created_at)) * 24) as avg_hours')
                ->value('avg_hours') ?? 0,
            1
        );

        $topArea = Area::query()
            ->leftJoin('needs', 'areas.id', '=', 'needs.area_id')
            ->selectRaw('areas.name as area_name, COUNT(needs.id) as needs_count')
            ->groupBy('areas.id', 'areas.name')
            ->orderByDesc('needs_count')
            ->first();

        return [
            Stat::make('نسبة الإنجاز', $completionRate . '%')
                ->description('الطلبات المُسلّمة من إجمالي الطلبات')
                ->color($completionRate >= 70 ? 'success' : 'warning'),

            Stat::make('متوسط زمن الاستجابة', $avgResponseHours . ' ساعة')
                ->description('من إنشاء الطلب حتى تحديثه كمُسلَّم')
                ->color('info'),

            Stat::make('أعلى منطقة بالطلبات', $topArea?->area_name ?? 'لا توجد بيانات')
                ->description('عدد الطلبات: ' . ($topArea?->needs_count ?? 0))
                ->color('gray'),
        ];
    }
}
