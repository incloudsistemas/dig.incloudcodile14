<?php

namespace App\Filament\Resources\Cms\ProductResource\Pages;

use App\Filament\Resources\Cms\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }

    protected function afterCreate(): void
    {
        // Force post create
        if (!$this->record->cmsPost) {
            $this->record->cmsPost()->create([]);
        }
    }
}
