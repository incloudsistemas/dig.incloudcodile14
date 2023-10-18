<?php

namespace App\Policies\Business;

use App\Models\Business\ShopBusiness;
use App\Models\User;

class ShopBusinessPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Shop] Vendas / Pedidos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ShopBusiness $business)
    {
        if ($user->hasPermissionTo(permission: 'Visualizar [Shop] Vendas / Pedidos')) {
            if ($user->hasRole(['Superadministrador', 'Administrador'])) {
                return true;
            }

            return $user->id === $business->user_id;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo(permission: 'Cadastrar [Shop] Vendas / Pedidos')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ShopBusiness $business)
    {
        if ($user->hasPermissionTo(permission: 'Editar [Shop] Vendas / Pedidos')) {
            if ($user->hasRole(['Superadministrador', 'Administrador'])) {
                return true;
            }

            return $user->id === $business->user_id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ShopBusiness $business)
    {
        if ($user->hasPermissionTo(permission: 'Deletar [Shop] Vendas / Pedidos')) {
            if ($user->hasRole(['Superadministrador', 'Administrador'])) {
                return true;
            }

            return $user->id === $business->user_id;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, ShopBusiness $business): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, ShopBusiness $business): bool
    // {
    //     //
    // }
}
