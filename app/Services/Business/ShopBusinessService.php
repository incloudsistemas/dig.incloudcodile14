<?php

namespace App\Services\Business;

use App\Enums\DefaultStatus;
use App\Models\Business\Business;
use App\Models\Shop\Product;
use App\Models\Shop\ProductVariantItem;
use App\Services\Shop\ProductVariantItemService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShopBusinessService
{
    public function __construct(
        protected Business $business,
        protected Product $product,
        protected ProductVariantItem $variantItem
    ) {
        $this->business = $business;
        $this->product = $product;
        $this->variantItem = $variantItem;
    }

    public function retrieveInventory(Business $business): void
    {
        $business->tradedItems->each(function ($item): void {
            $variantItem = ProductVariantItem::find($item->businessable_id);

            if (!$variantItem) {
                return;
            }

            $initialInventoryData = ProductVariantItemService::getInventoryData(inventory: $variantItem->inventory);

            $variantItem->inventory->increment('available', $item->quantity);
            $variantItem->inventory->refresh();

            $updatedInventoryData = ProductVariantItemService::getInventoryData(inventory: $variantItem->inventory);

            $activityData = [
                'changed_from' => $initialInventoryData,
                'changed_to'   => $updatedInventoryData,
                'description'  => 'Venda / Pedido excluído',
            ];

            ProductVariantItemService::createInventoryActivity($activityData, $variantItem->inventory);
        });
    }

    public function getProductVariantOptionsBySearch(?string $search): array
    {
        return $this->variantItem->whereHas('product', function (Builder $query) use ($search): Builder {
            return $query->where('name', 'like', "%{$search}%")
                ->whereJsonContains('publish_on->point_of_sale', true);
        })
            ->orWhere('sku', 'like', "%{$search}%")
            ->where('status', 1) // 1 - active
            ->limit(50)
            ->get()
            ->mapWithKeys(function ($item): array {
                return [$item->id => $item->display_name_with_sku];
            })
            ->toArray();
    }

    public function getProductVariantOptionLabel(?string $value): string
    {
        return $this->variantItem->find($value)?->display_name_with_sku;
    }

    public function getProductVariantInfos(?int $variantItemId): array
    {
        $result = [
            'unit_price'          => '',
            'unit_cost'           => '',
            'inventory_available' => '',
            'default_quantity'    => '',
            'price'               => '',
            'cost'                => '',
        ];

        if ($variantItemId) {
            $variantItem = $this->variantItem->findOrFail($variantItemId);
            $inventoryAvailable = $variantItem?->inventory->available ?? 0;

            $result['unit_price'] = $variantItem->display_price;
            $result['unit_cost'] = $variantItem->display_unit_cost;
            $result['inventory_available'] = $inventoryAvailable;
            $result['default_quantity'] = ($inventoryAvailable > 0) ? 1 : '';
            $result['price'] = !empty($result['default_quantity']) ? $variantItem->display_price : '';
            $result['cost'] = !empty($result['default_quantity']) ? $variantItem->display_unit_cost : '';
        }

        return $result;
    }

    public function getTotalPriceByVariantQuantity(?string $unitPrice, ?string $unitCost, ?int $quantity): array
    {
        $result = [
            'price' => '',
            'cost'  => '',
        ];

        $unitCost = $unitCost ?? 0;

        if ($unitPrice && $quantity) {
            $unitPrice = ConvertPtBrFloatStringToInt(value: $unitPrice);
            $unitCost  = ConvertPtBrFloatStringToInt(value: $unitCost);

            $price = $unitPrice * $quantity;
            $price = round(floatval($price) / 100, precision: 2);
            $result['price'] = number_format($price, 2, ',', '.');

            $cost = $unitCost * $quantity;
            $cost = round(floatval($cost) / 100, precision: 2);
            $result['cost']  = number_format($cost, 2, ',', '.');
        }

        return $result;
    }

    public function getTotalPriceOfAllVariants(array $tradedItems, ?string $discount): array
    {
        $result = [
            'price' => '',
            'cost'  => '',
        ];

        if ($tradedItems) {
            // Total price
            $prices = array_column($tradedItems, 'price');
            $prices = array_map(function ($price) {
                return ConvertPtBrFloatStringToInt(value: $price);
            }, $prices);

            $discount = !empty($discount) ? ConvertPtBrFloatStringToInt(value: $discount) : 0;

            $prices = array_sum($prices) - $discount;
            $prices = round(floatval($prices) / 100, precision: 2);
            $result['price'] = number_format($prices, 2, ',', '.');

            // Total cost
            $costs = array_column($tradedItems, 'cost');
            $costs = array_map(function ($cost) {
                return ConvertPtBrFloatStringToInt(value: $cost);
            }, $costs);

            $costs = array_sum($costs);
            $costs = round(floatval($costs) / 100, precision: 2);
            $result['cost'] = number_format($costs, 2, ',', '.');
        }

        return $result;
    }
}
