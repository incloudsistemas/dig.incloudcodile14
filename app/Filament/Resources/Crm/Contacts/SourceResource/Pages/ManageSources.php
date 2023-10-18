<?php

namespace App\Filament\Resources\Crm\Contacts\SourceResource\Pages;

use App\Filament\Resources\Crm\Contacts\SourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSources extends ManageRecords
{
    protected static string $resource = SourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
