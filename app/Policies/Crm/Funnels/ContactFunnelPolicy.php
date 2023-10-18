<?php

namespace App\Policies\Crm\Funnels;

use App\Models\Crm\Funnels\ContactFunnel;
use App\Models\User;

class ContactFunnelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [CRM] Funis de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactFunnel $funnel)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [CRM] Funis de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [CRM] Funis de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactFunnel $funnel)
    {
        if ($user->hasPermissionTo(permission: 'Editar [CRM] Funis de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactFunnel $funnel)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [CRM] Funis de Contatos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, ContactFunnel $funnel): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, ContactFunnel $funnel): bool
    // {
    //     //
    // }
}
