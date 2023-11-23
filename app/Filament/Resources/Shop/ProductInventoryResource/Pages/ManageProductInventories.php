<?php

namespace App\Filament\Resources\Shop\ProductInventoryResource\Pages;

use App\Filament\Resources\Shop\ProductInventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProductInventories extends ManageRecords
{
    protected static string $resource = ProductInventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
