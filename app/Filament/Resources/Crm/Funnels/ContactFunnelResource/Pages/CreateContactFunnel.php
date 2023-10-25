<?php

namespace App\Filament\Resources\Crm\Funnels\ContactFunnelResource\Pages;

use App\Filament\Resources\Crm\Funnels\ContactFunnelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContactFunnel extends CreateRecord
{
    protected static string $resource = ContactFunnelResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
