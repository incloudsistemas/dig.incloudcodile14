<?php

namespace App\Services\Shop;

use App\Enums\DefaultStatus;
use App\Models\Shop\ProductCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductCategoryService
{
    public function __construct(protected ProductCategory $category)
    {
        $this->category = $category;
    }

    public function getActiveCategoriesIgnoreRecord(ProductCategory $category): Builder
    {
        return $this->category->byStatuses(statuses: [1,]) // 1 - active
            ->where('id', '<>', $category->id);
    }

    public function getCategoriesWithSubcategories(Builder $query): Builder
    {
        return $query->byStatuses(statuses: [1,]) // 1 - active
            ->has('subcategories');
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

    public function anonymizeUniqueSlugWhenDeleted(ProductCategory $category): void
    {
        $category->slug = $category->slug . '//deleted_' . md5(uniqid());
        $category->save();
    }
}
