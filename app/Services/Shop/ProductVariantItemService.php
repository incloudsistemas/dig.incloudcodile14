<?php

namespace App\Services\Shop;

use App\Enums\DefaultStatus;
use App\Models\Shop\ProductVariantItem;
use Illuminate\Database\Eloquent\Builder;

class ProductVariantItemService
{
    public function __construct(protected ProductVariantItem $variantItem)
    {
        $this->variantItem = $variantItem;
    }

    public function getProfitAndMargin(?string $price, ?string $cost): array
    {
        $result = [
            'profit'        => '',
            'profit_margin' => ''
        ];

        if ($price && $cost) {
            $price = ConvertPtBrFloatStringToInt(value: $price);
            $cost  = ConvertPtBrFloatStringToInt(value: $cost);

            $profit = $price - $cost;
            $profitMargin = $profit / $price;

            $profit = round(floatval($profit) / 100, precision: 2);
            $profitMargin = round(floatval($profitMargin) * 100, precision: 2);

            $result['profit'] = number_format($profit, 2, ',', '.');
            $result['profit_margin'] = number_format($profitMargin, 2, ',', '.');
        }

        return $result;
    }

    public function tableSortByPrice(Builder $query, string $direction): Builder
    {
        return $query->orderBy('price', $direction);
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

    public function mutateRecordDataToEditUsing(ProductVariantItem $variantItem, array $data): array
    {
        $data['price'] = $variantItem->display_price;
        $data['compare_at_price'] = $variantItem->display_compare_at_price;
        $data['unit_cost'] = $variantItem->display_unit_cost;
        $data['profit'] = $variantItem->display_profit;
        $data['profit_margin'] = $variantItem->display_profit_margin;

        return $data;
    }

    public function ignoreDefaultVariantOption(Builder $query): Builder
    {
        return $query->where('name', '<>', 'Default Variant');
    }
}
