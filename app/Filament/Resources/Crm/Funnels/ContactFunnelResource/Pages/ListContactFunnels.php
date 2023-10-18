<?php

namespace App\Filament\Resources\Crm\Funnels\ContactFunnelResource\Pages;

use App\Filament\Resources\Crm\Funnels\ContactFunnelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactFunnels extends ListRecords
{
    protected static string $resource = ContactFunnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
