<?php

namespace App\Services\Shop;

use App\Enums\DefaultStatus;
use App\Models\Shop\ProductBrand;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductBrandService
{
    public function __construct(protected ProductBrand $brand)
    {
        $this->brand = $brand;
    }

    public function getActiveBrands(Builder $query): Builder
    {
        return $query->byStatuses(statuses: [1,]); // 1 - active
    }

    public function forceScopeActiveStatus(): Builder
    {
        return $this->brand->byStatuses(statuses: [1,]); // 1 - active
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

    public function anonymizeUniqueSlugWhenDeleted(ProductBrand $brand): void
    {
        $brand->slug = $brand->slug . '//deleted_' . md5(uniqid());
        $brand->save();
    }
}
