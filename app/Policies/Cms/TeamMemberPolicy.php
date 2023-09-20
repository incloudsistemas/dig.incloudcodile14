<?php

namespace App\Policies\Cms;

use App\Models\Cms\TeamMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Membros da Equipe')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TeamMember $teamMember)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Membros da Equipe')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Cms] Membros da Equipe')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TeamMember $teamMember)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Cms] Membros da Equipe')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TeamMember $teamMember)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Cms] Membros da Equipe')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, TeamMember $teamMember): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, TeamMember $teamMember): bool
    // {
    //     //
    // }
}
