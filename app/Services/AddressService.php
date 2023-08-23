<?php

namespace App\Services;

use App\Models\Address;
use Filament\Resources\RelationManagers\RelationManager;

class AddressService
{
    public function ensureOnlyOneMainAddress(array $data, Address $address, RelationManager $livewire): void
    {
        if ($data['is_main'] && $address->is_main === false) {
            $livewire->ownerRecord->addresses()->update(['is_main' => false]);
        }
    }
}
