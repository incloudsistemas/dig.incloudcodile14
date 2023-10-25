<?php

namespace App\Filament\Resources\Crm\Funnels\BusinessFunnelResource\Pages;

use App\Filament\Resources\Crm\Funnels\BusinessFunnelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBusinessFunnel extends CreateRecord
{
    protected static string $resource = BusinessFunnelResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }

    protected function afterCreate(): void
    {
        $count = $this->record->stages()
            ->max('order');

        $this->data['closing_stages']['done']['order'] = $count + 1;
        $this->data['closing_stages']['lost']['order'] = $count + 2;

        //  Create closing stages
        $this->record->stages()
            ->createMany([
                $this->data['closing_stages']['done'],
                $this->data['closing_stages']['lost']
            ]);
    }
}
