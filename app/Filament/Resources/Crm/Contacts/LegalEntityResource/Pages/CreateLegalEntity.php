<?php

namespace App\Filament\Resources\Crm\Contacts\LegalEntityResource\Pages;

use App\Filament\Resources\Crm\Contacts\LegalEntityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLegalEntity extends CreateRecord
{
    protected static string $resource = LegalEntityResource::class;

    protected function afterCreate(): void
    {
        $contact = $this->record->contact;

        // Force contact create
        if (!$contact) {
            $contact = $this->record->contact()
                ->create([]);
        }

        $rolesToSync = array_keys(array_filter($this->data['roles']));
        $contact->roles()
            ->sync($rolesToSync);
    }
}
