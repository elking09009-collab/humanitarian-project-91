<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Mail\AccountReviewStatusMail;
use App\Models\AccountReviewLog;
use App\Models\User;
use App\Notifications\AccountReviewedNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'طلبات الحسابات';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return (bool) ($user && ($user->role === 'admin' || $user->can_review_accounts));
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return (bool) ($user && ($user->role === 'admin' || $user->can_review_accounts));
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = User::where('role', '!=', 'admin')->where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(?Model $record = null): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->disabled(),
                Forms\Components\TextInput::make('phone')
                    ->label('الهاتف')
                    ->disabled(),
                Forms\Components\Select::make('role')
                    ->label('نوع الحساب')
                    ->options([
                        'admin' => 'أدمن',
                        'volunteer' => 'متطوع',
                        'organization' => 'منظمة',
                    ])
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->label('حالة الطلب')
                    ->options([
                        'pending' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('البريد الإلكتروني')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('الهاتف'),
                Tables\Columns\TextColumn::make('role')
                    ->label('نوع الحساب')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'أدمن',
                        'volunteer' => 'متطوع',
                        'organization' => 'منظمة',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => 'مقبول',
                        'pending' => 'قيد المراجعة',
                        'rejected' => 'مرفوض',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime('Y-m-d H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('تصفية بالحالة')
                    ->options([
                        'pending' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ])
                    ->default('pending'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('اعتماد')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (User $record): bool => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $oldStatus = $record->status;

                        $record->update([
                            'status' => 'approved',
                            'rejection_reason' => null,
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);

                        AccountReviewLog::create([
                            'user_id' => $record->id,
                            'reviewed_by' => Auth::id(),
                            'from_status' => $oldStatus,
                            'to_status' => 'approved',
                            'reason' => null,
                        ]);

                        Mail::to($record->email)->send(new AccountReviewStatusMail($record, 'approved'));
                        $record->notify(new AccountReviewedNotification('approved'));
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (User $record): bool => $record->status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->minLength(5)
                            ->maxLength(1000),
                    ])
                    ->action(function (User $record, array $data): void {
                        $oldStatus = $record->status;
                        $reason = trim((string) ($data['reason'] ?? ''));

                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $reason,
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                        ]);

                        AccountReviewLog::create([
                            'user_id' => $record->id,
                            'reviewed_by' => Auth::id(),
                            'from_status' => $oldStatus,
                            'to_status' => 'rejected',
                            'reason' => $reason,
                        ]);

                        Mail::to($record->email)->send(new AccountReviewStatusMail($record, 'rejected', $reason));
                        $record->notify(new AccountReviewedNotification('rejected', $reason));
                    }),
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('تصدير CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn () => route('export.users') . '?status=' . request('tableFilters.status.value', ''))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
