<?php

namespace App\Policies\Crm\Contacts;

use App\Models\Crm\Contacts\Role as ContactsRole;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [CRM] Tipos de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactsRole $role)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [CRM] Tipos de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [CRM] Tipos de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactsRole $role)
    {
        if ($user->hasPermissionTo(permission: 'Editar [CRM] Tipos de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactsRole $role)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [CRM] Tipos de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, ContactsRole $role): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, ContactsRole $role): bool
    // {
    //     //
    // }
}
