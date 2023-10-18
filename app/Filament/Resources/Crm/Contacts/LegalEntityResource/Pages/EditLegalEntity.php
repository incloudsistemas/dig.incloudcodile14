<?php

namespace App\Filament\Resources\Crm\Contacts\LegalEntityResource\Pages;

use App\Filament\Resources\Crm\Contacts\LegalEntityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLegalEntity extends EditRecord
{
    protected static string $resource = LegalEntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $contact = $this->record->contact;

        $data['roles'] = isset($contact->roles)
            ? $contact->roles->pluck('id', 'id')
                ->toArray()
            : [];

        return $data;
    }

    protected function afterSave(): void
    {
        $contact = $this->record->contact;

        $rolesToSync = array_keys(array_filter($this->data['roles']));
        $contact->roles()
            ->sync($rolesToSync);
    }
}
