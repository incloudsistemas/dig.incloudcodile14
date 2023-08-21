<?php

namespace App\Services\Permissions;

use App\Models\User;

class RoleService
{

    public static function getListOfRolesToAvoidByAuthUserRoles(User $user): array
    {
        $userRoles = $user->roles->pluck('id')->toArray();

        // avoid role 2 = client/customer, ALWAYS.
        // avoid role 1 = superadmin, if auth user role isn't superadmin.        
        // avoid role 3 = admin, if auth user role isn't superadmin or admin.
        if (in_array(1, $userRoles)) {
            $rolesToAvoid = [2];
        } elseif (in_array(3, $userRoles)) {
            $rolesToAvoid = [1, 2];
        } else {
            $rolesToAvoid = [1, 2, 3];
        }
        
        return $rolesToAvoid;
    }
}
