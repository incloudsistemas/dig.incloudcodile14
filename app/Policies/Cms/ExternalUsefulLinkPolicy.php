<?php

namespace App\Policies\Cms;

use App\Models\Cms\ExternalUsefulLink;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExternalUsefulLinkPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Links Externos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UsefulExternalLink $externalUsefulLink)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Links Externos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Cms] Links Externos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExternalUsefulLink $externalUsefulLink)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Cms] Links Externos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UsefulExternalLink $externalUsefulLink)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Cms] Links Externos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, UsefulExternalLink $externalUsefulLink): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, UsefulExternalLink $externalUsefulLink): bool
    // {
    //     //
    // }
}
