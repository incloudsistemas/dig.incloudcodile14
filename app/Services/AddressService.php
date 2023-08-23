<?php

namespace App\Services;

use App\Models\Address;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\DeleteAction;

class AddressService
{
    public function __construct(protected Address $address)
    {
        $this->address = $address;
    }

    public function ensureUniqueMainAddress(array $data, Address $address, RelationManager $livewire): void
    {
        if (($address->is_main === false && $data['is_main']) || (is_null($address->is_main) && $data['is_main'])) {
            $livewire->ownerRecord->addresses()->update(['is_main' => false]);
        }
    }

    public function preventMainAddressDeleteWhenMultiple(DeleteAction $action, Address $address, RelationManager $livewire): void
    {
        if ($address->is_main && $livewire->ownerRecord->addresses->count() > 1) {
            Notification::make()
                ->title('Não é possível excluir o endereço principal quando se tem outros endereços cadastrados.')
                ->warning()
                ->body('Para excluir, você deve primeiro definir outro endereço como principal.')
                ->send();

            // $action->cancel();
            $action->halt();
        }
    }
}
