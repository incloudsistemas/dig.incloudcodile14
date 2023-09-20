<?php

namespace App\Filament\Resources\Cms\PartnerResource\Pages;

use App\Filament\Resources\Cms\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePartners extends ManageRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
