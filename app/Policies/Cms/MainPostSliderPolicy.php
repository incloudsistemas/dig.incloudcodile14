<?php

namespace App\Policies\Cms;

use App\Models\Cms\PostSlider;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MainPostSliderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Sliders')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PostSlider $slider)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Cms] Sliders')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Cms] Sliders')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PostSlider $slider)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Cms] Sliders')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PostSlider $slider)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Cms] Sliders')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, PostSlider $slider): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, PostSlider $slider): bool
    // {
    //     //
    // }
}
