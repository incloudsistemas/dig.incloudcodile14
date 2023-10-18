<?php

namespace App\Filament\Resources\Crm\Funnels\BusinessFunnelResource\Pages;

use App\Filament\Resources\Crm\Funnels\BusinessFunnelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessFunnels extends ListRecords
{
    protected static string $resource = BusinessFunnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
