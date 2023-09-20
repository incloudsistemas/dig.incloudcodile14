<?php

namespace App\Services\Cms;

use App\Enums\Cms\ProductRole;
use App\Models\Cms\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductService
{
    public function __construct(protected Product $product)
    {
        $this->product = $product;
    }

    public function tableSearchByRole(Builder $query, string $search): Builder
    {
        $roles = ProductRole::asSelectArray();

        $matchingRoles = [];
        foreach ($roles as $index => $role) {
            if (stripos($role, $search) !== false) {
                $matchingRoles[] = $index;
            }
        }

        if ($matchingRoles) {
            return $query->whereIn('role', $matchingRoles);
        }

        return $query;
    }

    public function tableSortByRole(Builder $query, string $direction): Builder
    {
        $roles = ProductRole::asSelectArray();

        $caseParts = [];
        $bindings = [];

        foreach ($roles as $key => $role) {
            $caseParts[] = "WHEN ? THEN ?";
            $bindings[] = $key;
            $bindings[] = $role;
        }

        $orderByCase = "CASE role " . implode(' ', $caseParts) . " END";

        return $query->orderByRaw("$orderByCase $direction", $bindings);
    }
}
