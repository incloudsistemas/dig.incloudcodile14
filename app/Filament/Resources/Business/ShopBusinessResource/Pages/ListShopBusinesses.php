<?php

namespace App\Filament\Resources\Business\ShopBusinessResource\Pages;

use App\Filament\Resources\Business\ShopBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopBusinesses extends ListRecords
{
    protected static string $resource = ShopBusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
