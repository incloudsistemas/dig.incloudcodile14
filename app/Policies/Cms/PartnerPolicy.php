<?php

namespace App\Policies\Cms;

use App\Models\Cms\Partner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PartnerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Parceiros')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Partner $partner)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Parceiros')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Cms] Parceiros')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Partner $partner)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Cms] Parceiros')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Partner $partner)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Cms] Parceiros')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Partner $partner): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Partner $partner): bool
    // {
    //     //
    // }
}
