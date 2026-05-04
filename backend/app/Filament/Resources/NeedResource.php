<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NeedResource\Pages;
use App\Models\Area;
use App\Models\Need;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class NeedResource extends Resource
{
    protected static ?string $model = Need::class;
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';
    protected static ?string $navigationLabel = 'الاحتياجات';
    protected static ?int $navigationSort = 2;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('area_id')
                    ->label('المنطقة')
                    ->options(fn () => Area::withoutGlobalScopes()
                        ->get()
                        ->mapWithKeys(fn (Area $area) => [
                            $area->id => $area->getTranslation('name', 'ar', false)
                                ?? $area->getTranslation('name', 'en', false)
                                ?? (string) $area->id,
                        ]))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('نوع الاحتياج')
                    ->options([
                        'food' => 'طعام',
                        'water' => 'مياه',
                        'medicine' => 'دواء',
                        'shelter' => 'مأوى',
                        'other' => 'أخرى',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'معلقة',
                        'delivered' => 'تم إيصالها',
                    ])
                    ->default('pending') // القيمة الافتراضية
                    ->required(),

                Forms\Components\Section::make('الملاحظات')
                    ->schema([
                        Forms\Components\Textarea::make('notes.ar')
                            ->label('ملاحظات (عربي)'),
                        Forms\Components\Textarea::make('notes.en')
                            ->label('Notes (English)'),
                        Forms\Components\Textarea::make('notes.fr')
                            ->label('Notes (Français)'),
                    ])
                    ->columns(3),

                \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->label('صور مرفقة')
                    ->collection('images')
                    ->image()
                    ->multiple()
                    ->maxFiles(5)
                    ->reorderable(),
            ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('area.name')
                ->label('المنطقة')
                ->getStateUsing(fn ($record) => optional($record->area)->getTranslation('name', 'ar', false)
                    ?? optional($record->area)->getTranslation('name', 'en', false)
                    ?? '-'),
            Tables\Columns\TextColumn::make('type')
                ->label('نوع الاحتياج')
                ->getStateUsing(fn ($record) => match($record->type) {
                    'food' => 'طعام',
                    'water' => 'مياه',
                    'medicine' => 'دواء',
                    'shelter' => 'مأوى',
                    'other' => 'أخرى',
                    default => $record->type,
                }),
            Tables\Columns\TextColumn::make('quantity')->label('الكمية'),
            Tables\Columns\TextColumn::make('status')
                ->label('الحالة')
                ->getStateUsing(fn ($record) => match($record->status) {
                    'pending' => 'معلقة',
                    'delivered' => 'تم إيصالها',
                    default => $record->status,
                }),
            Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->date(),
        ])
        ->actions([
            Tables\Actions\EditAction::make()->label('تعديل'),
            Tables\Actions\DeleteAction::make()->label('حذف'),
            Tables\Actions\Action::make('comments')
                ->label(fn (Need $record) => 'تعليقات (' . $record->comments()->count() . ')')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('info')
                ->modalHeading(fn (Need $record) => 'تعليقات: ' . $record->type . ' - ' . (
                    $record->area?->getTranslation('name', 'ar', false)
                    ?? $record->area?->getTranslation('name', 'en', false)
                    ?? ''
                ))
                ->modalContent(fn (Need $record) => view('filament.need-comments', ['need' => $record]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('إغلاق'),
        ])
        ->headerActions([
            Tables\Actions\Action::make('export_csv')
                ->label('تصدير CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('export.needs'))
                ->openUrlInNewTab(),
        ]);
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNeeds::route('/'),
            'create' => Pages\CreateNeed::route('/create'),
            'edit' => Pages\EditNeed::route('/{record}/edit'),
        ];
    }

    // تقييد CRUD للـ Admin فقط
    public static function canCreate(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function canEdit(?Model $record = null): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function canDelete(?Model $record = null): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public static function canView(?Model $record = null): bool
    {
        return Auth::user() !== null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'admin';
    }
}

