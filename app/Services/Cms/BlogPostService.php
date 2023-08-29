<?php

namespace App\Services\Cms;

use App\Enums\Cms\BlogRole;
use App\Models\Cms\BlogPost;
use Illuminate\Database\Eloquent\Builder;

class BlogPostService
{
    public function __construct(protected BlogPost $blog)
    {
        $this->blog = $blog;
    }    

    public function tableSearchByRole(Builder $query, string $search): Builder
    {
        $roles = BlogRole::asSelectArray();

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
        $roles = BlogRole::asSelectArray();

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
