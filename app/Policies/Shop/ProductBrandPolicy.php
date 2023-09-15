<?php

namespace App\Policies\Shop;

use App\Models\Shop\ProductBrand;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductBrandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Shop] Marcas')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductBrand $brand)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Shop] Marcas')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Shop] Marcas')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductBrand $brand)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Shop] Marcas')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductBrand $brand)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Shop] Marcas')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, ProductBrand $brand): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, ProductBrand $brand): bool
    // {
    //     //
    // }
}
