<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'المناطق المتضررة';
    protected static ?int $navigationSort = 1;
public static function shouldRegisterNavigation(): bool
{
    return Auth::user()?->role === 'admin';
}

public static function canView(?Model $record = null): bool
{
    return Auth::user()?->role === 'admin';
}
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


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('الاسم')
                    ->schema([
                        Forms\Components\TextInput::make('name.ar')->label('الاسم (عربي)')->required(),
                        Forms\Components\TextInput::make('name.en')->label('Name (English)'),
                        Forms\Components\TextInput::make('name.fr')->label('Nom (Français)'),
                    ])->columns(3),

                Forms\Components\Section::make('الوصف')
                    ->schema([
                        Forms\Components\Textarea::make('description.ar')->label('الوصف (عربي)'),
                        Forms\Components\Textarea::make('description.en')->label('Description (English)'),
                        Forms\Components\Textarea::make('description.fr')->label('Description (Français)'),
                    ])->columns(3),

                Forms\Components\TextInput::make('latitude')->numeric()->required()->label('خط العرض'),
                Forms\Components\TextInput::make('longitude')->numeric()->required()->label('خط الطول'),

                Forms\Components\Select::make('priority_level')
                    ->label('الأولوية')
                    ->options(['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية'])
                    ->required(),

                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options(['active' => 'نشطة', 'inactive' => 'غير نشطة'])
                    ->required(),
            ]);
    }

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('المنطقة')
                ->getStateUsing(fn ($record) => $record->getTranslation('name', 'ar', false)
                    ?? $record->getTranslation('name', 'en', false)
                    ?? '-'),
            Tables\Columns\TextColumn::make('priority_level')
                ->label('الأولوية')
                ->getStateUsing(fn ($record) => match($record->priority_level) {
                    'low' => 'منخفضة',
                    'medium' => 'متوسطة',
                    'high' => 'عالية',
                    default => $record->priority_level,
                }),
            Tables\Columns\TextColumn::make('status')
                ->label('الحالة')
                ->getStateUsing(fn ($record) => match($record->status) {
                    'active' => 'نشطة',
                    'inactive' => 'غير نشطة',
                    default => $record->status,
                }),
            Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->date(),
        ])
        ->actions([
            Tables\Actions\EditAction::make()->label('تعديل'),
            Tables\Actions\DeleteAction::make()->label('حذف'),
        ]);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}
