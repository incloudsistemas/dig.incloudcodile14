<?php

namespace App\Filament\Resources\Cms\ServiceResource\Pages;

use App\Filament\Resources\Cms\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }

    protected function afterCreate(): void
    {
        // Force post create
        if (!$this->record->cmsPost) {
            $this->record->cmsPost()->create();
        }
    }
}
