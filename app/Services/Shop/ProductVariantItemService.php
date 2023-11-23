<?php

namespace App\Services\Shop;

use App\Enums\DefaultStatus;
use App\Models\Shop\ProductInventory;
use App\Models\Shop\ProductVariantItem;
use Illuminate\Database\Eloquent\Builder;

class ProductVariantItemService
{
    public function __construct(protected ProductVariantItem $variantItem)
    {
        $this->variantItem = $variantItem;
    }

    public function mutateRecordDataToEditUsing(ProductVariantItem $variantItem, array $data): array
    {
        $data['price'] = $variantItem->display_price;
        $data['compare_at_price'] = $variantItem->display_compare_at_price;
        $data['unit_cost'] = $variantItem->display_unit_cost;
        $data['profit'] = $variantItem->display_profit;
        $data['profit_margin'] = $variantItem->display_profit_margin;

        $data['inventory'] = $this->getInventoryData($variantItem->inventory);

        return $data;
    }

    public function editAction(ProductVariantItem $variantItem, array $data): ProductVariantItem
    {
        $data['activity']['changed_from'] = $this->getInventoryData($variantItem->inventory);
        $data['activity']['changed_to'] = $data['inventory'] ?? null;

        $variantItem->update($data);

        if (isset($data['inventory'])) {
            $inventory = $variantItem->inventory()->updateOrCreate(
                ['variant_item_id' => $variantItem->id],
                $data['inventory']
            );

            $this->createInventoryActivity(data: $data['activity'], inventory: $inventory);
        }

        return $variantItem;
    }

    public static function createInventoryActivity(array $data, ProductInventory $inventory): void
    {
        $changes = array_diff_assoc($data['changed_to'], $data['changed_from']);

        if (!empty($changes) && auth()->check()) {
            $data['user_id'] = auth()->user()->id;

            $inventory->inventoryActivities()
                ->create($data);
        }
    }

    public static function getInventoryData(?ProductInventory $inventory): array
    {
        $fields = [
            'available',
            'committed',
            'unavailable_damaged',
            'unavailable_quality_control',
            'unavailable_safety',
            'unavailable_other',
            'to_receive',
            'total'
        ];

        $data = [];

        foreach ($fields as $field) {
            $data[$field] = isset($inventory) ? $inventory->$field : 0;
        }

        return $data;
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

    public function ignoreDefaultVariantOption(Builder $query): Builder
    {
        return $query->where('name', '<>', 'Default Variant');
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

    public function getInventoryTotal(array $data): int
    {
        $total = $data['available'] + $data['committed'] + $data['unavailable_damaged'] + $data['unavailable_quality_control'] + $data['unavailable_safety'] + $data['unavailable_other'];
        return $total;
    }
}
