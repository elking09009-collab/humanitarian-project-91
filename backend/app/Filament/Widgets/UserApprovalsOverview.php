<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserApprovalsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $pendingUsers = User::where('role', '!=', 'admin')
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make('طلبات الحسابات المعلقة', $pendingUsers)
                ->description('اضغط لمراجعة طلبات التسجيل')
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingUsers > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.users.index')),
        ];
    }
}
