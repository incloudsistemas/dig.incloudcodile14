<?php

namespace App\Policies\Shop;

use App\Models\Shop\ProductInventory;
use App\Models\User;

class ProductInventoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Shop] Estoques')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductInventory $inventory)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Shop] Estoques')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Shop] Estoques')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductInventory $inventory)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Shop] Estoques')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductInventory $inventory)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Shop] Estoques')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, ProductInventory $inventory): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, ProductInventory $inventory): bool
    // {
    //     //
    // }
}
