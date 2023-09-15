<?php

namespace App\Policies\Shop;

use App\Models\Shop\ProductCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Shop] Categorias')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductCategory $category)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Shop] Categorias')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Shop] Categorias')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductCategory $category)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Shop] Categorias')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductCategory $category)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Shop] Categorias')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, ProductCategory $category): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, ProductCategory $category): bool
    // {
    //     //
    // }
}
