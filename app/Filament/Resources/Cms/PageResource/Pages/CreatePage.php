<?php

namespace App\Filament\Resources\Cms\PageResource\Pages;

use App\Filament\Resources\Cms\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function afterCreate(): void
    {
        // Force post create
        if (!$this->record->cmsPost) {
            $this->record->cmsPost()->create([]);
        }
    }
}
