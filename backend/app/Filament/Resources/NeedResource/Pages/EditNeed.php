<?php

namespace App\Filament\Resources\NeedResource\Pages;

use App\Filament\Resources\NeedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNeed extends EditRecord
{
    protected static string $resource = NeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
