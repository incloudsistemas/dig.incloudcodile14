<?php

namespace App\Policies\Permissions;

use App\Models\Permissions\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar Níveis de Acessos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar Níveis de Acessos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar Níveis de Acessos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role)
    {
        if ($role->id === 1) {
            return false;
        }

        if ($user->hasPermissionTo(permission: 'Editar Níveis de Acessos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role)
    {
        if ($role->id === 1) {
            return false;
        }

        if ($user->hasPermissionTo(permission: 'Deletar Níveis de Acessos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Role $role): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Role $role): bool
    // {
    //     //
    // }
}
