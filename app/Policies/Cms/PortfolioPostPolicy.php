<?php

namespace App\Policies\Cms;

use App\Models\Cms\PortfolioPost;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PortfolioPostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Portfólio')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PortfolioPost $portfolio)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Portfólio')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Cms] Portfólio')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PortfolioPost $portfolio)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Cms] Portfólio')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PortfolioPost $portfolio)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Cms] Portfólio')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, PortfolioPost $portfolio): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, PortfolioPost $portfolio): bool
    // {
    //     //
    // }
}
