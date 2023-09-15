<?php

namespace App\Services\Permissions;

use App\Models\Permissions\Role;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class RoleService
{
    public function __construct(protected Role $role)
    {
        $this->role = $role;
    }

    public static function getArrayOfRolesToAvoidByAuthUserRoles(User $user): array
    {
        $userRoles = $user->roles->pluck('id')->toArray();

        // avoid role 2 = client/customer, ALWAYS.
        // avoid role 1 = superadmin, if auth user role isn't superadmin.
        // avoid role 3 = admin, if auth user role isn't superadmin or admin.
        if (in_array(1, $userRoles)) {
            $rolesToAvoid = [2];
        } elseif (in_array(3, $userRoles)) {
            $rolesToAvoid = [1, 2];
        } else {
            $rolesToAvoid = [1, 2, 3];
        }

        return $rolesToAvoid;
    }

    public function getRolesbyAuthUserRoles(Builder $query): Builder
    {
        $user = auth()->user();
        $rolesToAvoid = static::getArrayOfRolesToAvoidByAuthUserRoles($user);

        return $query->whereNotIn('id', $rolesToAvoid);
    }

    /**
     * $action can be:
     * Filament\Tables\Actions\DeleteAction;
     * Filament\Actions\DeleteAction;
     */
    public function preventRoleDeleteWithRelations($action, Role $role): void
    {
        if ($role->users->count() > 0) {
            Notification::make()
                ->title('Este nível de acesso possui usuários relacionados.')
                ->warning()
                ->body('Para excluir, você deve primeiro desvincular todos os usuários deste nível de acesso.')
                ->send();

            // $action->cancel();
            $action->halt();
        }
    }
}
