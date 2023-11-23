<?php

namespace App\Services\Shop;

use App\Models\Shop\ProductInventory;
use App\Models\Shop\ProductVariantItem;
use Illuminate\Database\Eloquent\Builder;

class ProductInventoryService
{
    public function __construct(
        protected ProductInventory $inventory,
        protected ProductVariantItem $variantItem
    ) {
        $this->inventory = $inventory;
        $this->variantItem = $variantItem;
    }

    public function mutateRecordDataToEditUsing(ProductInventory $inventory, array $data): array
    {
        // $data['sku'] = $inventory->variantItem->sku;
        // $data['barcode'] = $inventory->variantItem->barcode;
        // $data['inventory_management'] = $inventory->variantItem->inventory_management;
        // $data['inventory_out_allowed'] = $inventory->variantItem->inventory_out_allowed;
        // $data['inventory_security_alert'] = $inventory->variantItem->inventory_security_alert;
        $data['total'] = $inventory->total;

        return $data;
    }

    public function editAction(ProductInventory $inventory, array $data): ProductInventory
    {
        $initialInventoryData = ProductVariantItemService::getInventoryData(inventory: $inventory);

        $inventory->update($data);

        $updatedInventoryData = ProductVariantItemService::getInventoryData(inventory: $inventory);

        $activityData = [
            'changed_from' => $initialInventoryData,
            'changed_to'   => $updatedInventoryData,
            'description'  => 'Controle de estoque',
        ];

        ProductVariantItemService::createInventoryActivity($activityData, $inventory);

        return $inventory;
    }

    public function tableSearchByProductVariantItem(Builder $query, string $search): Builder
    {
        return $query->whereHas('variantItem', function (Builder $query) use ($search): Builder {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('product', function (Builder $query) use ($search): Builder {
                    return $query->where('name', 'like', "%{$search}%");
                });
        });
    }

    public function tableSortByProductVariantItem(Builder $query, string $direction): Builder
    {
        return $query->join('shop_product_variant_items', 'shop_product_inventories.variant_item_id', '=', 'shop_product_variant_items.id')
            ->join('shop_products', 'shop_product_variant_items.product_id', '=', 'shop_products.id')
            ->orderBy('shop_products.name', $direction);
    }
}
