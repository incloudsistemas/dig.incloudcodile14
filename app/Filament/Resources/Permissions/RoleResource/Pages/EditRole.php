<?php

namespace App\Filament\Resources\Permissions\RoleResource\Pages;

use App\Filament\Resources\Permissions\RoleResource;
use App\Models\Permissions\Role;
use App\Services\Permissions\RoleService;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(
                    function (RoleService $service, DeleteAction $action, Role $role): void {
                        $service->preventRoleDeleteWithRelations($action, $role);
                    }
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
