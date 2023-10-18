<?php

namespace App\Policies\Crm\Contacts;

use App\Models\Crm\Contacts\LegalEntity;
use App\Models\User;

class LegalEntityPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [CRM] Contatos P. Jurídicas')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LegalEntity $legalEntity)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [CRM] Contatos P. Jurídicas')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [CRM] Contatos P. Jurídicas')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LegalEntity $legalEntity)
    {
        if ($user->hasPermissionTo(permission: 'Editar [CRM] Contatos P. Jurídicas')) {
            if ($user->hasRole(['Superadministrador', 'Administrador'])) {
                return true;
            }

            return $user->id === $legalEntity->contact->user_id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LegalEntity $legalEntity)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [CRM] Contatos P. Jurídicas')) {
            if ($user->hasRole(['Superadministrador', 'Administrador'])) {
                return true;
            }

            return $user->id === $legalEntity->contact->user_id;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, LegalEntity $legalEntity): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, LegalEntity $legalEntity): bool
    // {
    //     //
    // }
}
