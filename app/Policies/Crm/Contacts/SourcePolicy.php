<?php

namespace App\Policies\Crm\Contacts;

use App\Models\Crm\Contacts\Source;
use App\Models\User;

class SourcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [CRM] Origens dos Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Source $source)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [CRM] Origens dos Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [CRM] Origens dos Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Source $source)
    {
        if ($user->hasPermissionTo(permission: 'Editar [CRM] Origens dos Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Source $source)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [CRM] Origens dos Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Source $source): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Source $source): bool
    // {
    //     //
    // }
}
