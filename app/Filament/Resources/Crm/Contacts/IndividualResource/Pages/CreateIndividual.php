<?php

namespace App\Filament\Resources\Crm\Contacts\IndividualResource\Pages;

use App\Filament\Resources\Crm\Contacts\IndividualResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIndividual extends CreateRecord
{
    protected static string $resource = IndividualResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }

    protected function afterCreate(): void
    {
        $contact = $this->record->contact;

        // Force contact create
        if (!$contact) {
            $contact = $this->record->contact()
                ->create();
        }

        $rolesToSync = array_keys(array_filter($this->data['roles']));
        $contact->roles()
            ->sync($rolesToSync);
    }
}
