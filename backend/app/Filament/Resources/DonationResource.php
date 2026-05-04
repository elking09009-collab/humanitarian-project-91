<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'دفتر التبرعات';
    protected static ?string $modelLabel = 'تبرع';
    protected static ?string $pluralModelLabel = 'دفتر التبرعات';
    protected static ?int $navigationSort = 8;

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
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('donor.email')->label('المتبرع')->placeholder('—')->searchable(),
                Tables\Columns\TextColumn::make('amount')->label('المبلغ')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('cause')->label('السبب')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('prev_hash')->label('Prev Hash')->copyable()->limit(18)->toggleable(),
                Tables\Columns\TextColumn::make('hash')->label('Hash')->copyable()->limit(18),
                Tables\Columns\IconColumn::make('is_chain_root')
                    ->label('Genesis')
                    ->boolean()
                    ->getStateUsing(fn (Donation $record) => empty($record->prev_hash)),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonations::route('/'),
        ];
    }
}
