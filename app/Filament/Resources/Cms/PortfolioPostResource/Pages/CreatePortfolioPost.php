<?php

namespace App\Filament\Resources\Cms\PortfolioPostResource\Pages;

use App\Filament\Resources\Cms\PortfolioPostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePortfolioPost extends CreateRecord
{
    protected static string $resource = PortfolioPostResource::class;

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
