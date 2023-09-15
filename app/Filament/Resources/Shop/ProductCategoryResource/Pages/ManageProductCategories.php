<?php

namespace App\Filament\Resources\Shop\ProductCategoryResource\Pages;

use App\Filament\Resources\Shop\ProductCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProductCategories extends ManageRecords
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
