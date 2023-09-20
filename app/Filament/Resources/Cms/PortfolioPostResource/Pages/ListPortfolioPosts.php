<?php

namespace App\Filament\Resources\Cms\PortfolioPostResource\Pages;

use App\Filament\Resources\Cms\PortfolioPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPortfolioPosts extends ListRecords
{
    protected static string $resource = PortfolioPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
