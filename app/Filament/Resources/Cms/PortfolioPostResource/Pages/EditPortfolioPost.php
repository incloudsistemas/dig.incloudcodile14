<?php

namespace App\Filament\Resources\Cms\PortfolioPostResource\Pages;

use App\Filament\Resources\Cms\PortfolioPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPortfolioPost extends EditRecord
{
    protected static string $resource = PortfolioPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
