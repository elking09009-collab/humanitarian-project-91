<?php

namespace App\Filament\Resources\AccountReviewLogResource\Pages;

use App\Filament\Resources\AccountReviewLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAccountReviewLogs extends ListRecords
{
    protected static string $resource = AccountReviewLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
