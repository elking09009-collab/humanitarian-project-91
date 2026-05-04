<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountReviewLogResource\Pages;
use App\Models\AccountReviewLog;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AccountReviewLogResource extends Resource
{
    protected static ?string $model = AccountReviewLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'سجل المراجعات';
    protected static ?string $modelLabel = 'سجل مراجعة';
    protected static ?string $pluralModelLabel = 'سجلات المراجعات';
    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return (bool) ($user && $user->role === 'admin');
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return (bool) ($user && $user->role === 'admin');
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit(?\Illuminate\Database\Eloquent\Model $record = null): bool { return false; }
    public static function canDelete(?\Illuminate\Database\Eloquent\Model $record = null): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('المراجع')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('from_status')
                    ->label('من حالة')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'approved' => 'مقبول',
                        'pending'  => 'قيد المراجعة',
                        'rejected' => 'مرفوض',
                        default    => $state ?? '-',
                    }),
                Tables\Columns\TextColumn::make('to_status')
                    ->label('إلى حالة')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'approved' => 'مقبول',
                        'pending'  => 'قيد المراجعة',
                        'rejected' => 'مرفوض',
                        default    => $state ?? '-',
                    }),
                Tables\Columns\TextColumn::make('reason')
                    ->label('السبب')
                    ->limit(50)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('reviewed_by')
                    ->label('تصفية بالمراجع')
                    ->options(fn () => User::where('role', 'admin')
                        ->orWhere('can_review_accounts', true)
                        ->pluck('name', 'id')
                        ->toArray()),
                Tables\Filters\SelectFilter::make('to_status')
                    ->label('تصفية بالإجراء')
                    ->options([
                        'approved' => 'قبول',
                        'rejected' => 'رفض',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->label('تصفية بالتاريخ')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('من تاريخ'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'],  fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                            ->when($data['until'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d));
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('تصدير CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn () => route('export.audit-logs'))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountReviewLogs::route('/'),
        ];
    }
}
