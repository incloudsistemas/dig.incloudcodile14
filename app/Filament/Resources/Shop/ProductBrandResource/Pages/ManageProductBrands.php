<?php

namespace App\Filament\Resources\Shop\ProductBrandResource\Pages;

use App\Filament\Resources\Shop\ProductBrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProductBrands extends ManageRecords
{
    protected static string $resource = ProductBrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
