<?php

namespace App\Services\Cms;

use App\Enums\DefaultStatus;
use App\Models\Cms\PostCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;

class PostCategoryService
{
    public function __construct(protected PostCategory $category)
    {
        $this->category = $category;
    }

    public function forceScopeActiveStatus(): Builder
    {
        return $this->category->byStatuses(statuses: [1,]); // 1 - active
    }

    public function tableSearchByStatus(Builder $query, string $search): Builder
    {
        $statuses = DefaultStatus::asSelectArray();

        $matchingStatuses = [];
        foreach ($statuses as $index => $status) {
            if (stripos($status, $search) !== false) {
                $matchingStatuses[] = $index;
            }
        }

        if ($matchingStatuses) {
            return $query->whereIn('status', $matchingStatuses);
        }

        return $query;
    }

    public function tableSortByStatus(Builder $query, string $direction): Builder
    {
        $statuses = DefaultStatus::asSelectArray();

        $caseParts = [];
        $bindings = [];

        foreach ($statuses as $key => $status) {
            $caseParts[] = "WHEN ? THEN ?";
            $bindings[] = $key;
            $bindings[] = $status;
        }

        $orderByCase = "CASE status " . implode(' ', $caseParts) . " END";

        return $query->orderByRaw("$orderByCase $direction", $bindings);
    }
}
