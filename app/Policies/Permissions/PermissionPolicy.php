<?php

namespace App\Policies\Permissions;

use App\Models\Permissions\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {        
        // return $user->hasPermissionTo(permission: 'Visualizar Permissões');
        return $user->hasRole('Superadministrador');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        // return $user->hasPermissionTo(permission: 'Visualizar Permissões');
        return $user->hasRole('Superadministrador');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // return $user->hasPermissionTo(permission: 'Cadastrar Permissões');
        return $user->hasRole('Superadministrador');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        // return $user->hasPermissionTo(permission: 'Editar Permissões');
        return $user->hasRole('Superadministrador');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        // return $user->hasPermissionTo(permission: 'Deletar Permissões');
        return $user->hasRole('Superadministrador');
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Permission $permission): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Permission $permission): bool
    // {
    //     //
    // }
}
