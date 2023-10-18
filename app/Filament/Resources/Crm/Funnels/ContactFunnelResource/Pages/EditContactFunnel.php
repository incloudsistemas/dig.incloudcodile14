<?php

namespace App\Filament\Resources\Crm\Funnels\ContactFunnelResource\Pages;

use App\Filament\Resources\Crm\Funnels\ContactFunnelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactFunnel extends EditRecord
{
    protected static string $resource = ContactFunnelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
