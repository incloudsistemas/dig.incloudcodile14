<?php

namespace App\Filament\Resources\Business\ShopBusinessResource\Pages;

use App\Filament\Resources\Business\ShopBusinessResource;
use App\Models\Business\Business;
use App\Models\Business\ShopBusiness;
use App\Models\Business\TradedItem;
use App\Models\Shop\ProductInventory;
use App\Models\Shop\ProductVariantItem;
use App\Services\Business\ShopBusinessService;
use App\Services\Shop\ProductVariantItemService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class EditShopBusiness extends EditRecord
{
    protected static string $resource = ShopBusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(
                    fn (ShopBusinessService $service, ShopBusiness $business) =>
                    $service->retrieveInventory(business: $business)
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $mainFunnel = $this->record->funnels->first();

        $mainFunnelStage = $this->record->funnelStages->where('funnel_id', $mainFunnel->id)
            ->first();

        $data['funnel_id'] = $mainFunnel->id;
        $data['funnel_stage_id'] = $mainFunnelStage->funnel_stage_id ?? null;

        $data['shipping_cost'] = $this->record->display_shipping_cost;
        $data['price'] = $this->record->display_price;
        $data['cost'] = $this->record->display_cost;
        $data['discount'] = $this->record->display_discount;

        $data['traded_items'] = $this->record->tradedItems->map(function ($tradedItem): array {
            $variantItem = ProductVariantItem::findOrFail($tradedItem->businessable_id);

            return [
                'product_variant_item_id' => $variantItem->id,
                'quantity'                => $tradedItem->quantity,
                'unit_price'              => $tradedItem->display_unit_price,
                'price'                   => $tradedItem->display_price,
                'unit_cost'               => $tradedItem->display_unit_cost,
                'cost'                    => $tradedItem->display_cost,
                'inventory_available'     => $variantItem->inventory->available + $tradedItem->quantity,
            ];
        })
            ->all();

        return $data;
    }

    protected function afterSave(): void
    {
        $this->updateFunnelStage();
        $this->updateTradedItems();
    }

    protected function updateFunnelStage(): void
    {
        $data['funnel_stages']['funnel_stage_id'] = $this->data['funnel_stage_id'];

        $morphMap  = Relation::morphMap();
        $modelType = array_search(get_class($this->record), $morphMap, true);

        $this->record->funnelStages()->updateOrCreate(
            ['model_type' => $modelType, 'model_id' => $this->record->id, 'funnel_id' => $this->data['funnel_id']],
            $data['funnel_stages']
        );
    }

    protected function updateTradedItems(): void
    {
        $currentTradedItems = $this->record->tradedItems;

        foreach ($currentTradedItems as $currentItem) {
            $this->handleTradedItem($currentItem);
        }

        $this->addTradedItem();
    }

    protected function handleTradedItem(TradedItem $currentItem): void
    {
        $found = false;

        foreach ($this->data['traded_items'] as $index => $updatedItem) {
            if ($updatedItem['product_variant_item_id'] == $currentItem->businessable_id) {
                $found = true;
                $this->updateTradedItemAndInventory(currentItem: $currentItem, updatedItem: $updatedItem, index: $index);
                break;
            }
        }

        if (!$found) {
            $this->removeTradedItemAndRestoreInventory(currentItem: $currentItem);
        }
    }

    protected function updateTradedItemAndInventory(TradedItem $currentItem, array $updatedItem, string $index): void
    {
        // Extract the quantity difference calculation logic
        $quantityDifference = $updatedItem['quantity'] - $currentItem->quantity;
        $variantItem = ProductVariantItem::find($updatedItem['product_variant_item_id']);

        $initialInventoryData = ProductVariantItemService::getInventoryData(inventory: $variantItem->inventory);

        $variantItem->inventory->decrement('available', $quantityDifference);
        $variantItem->inventory->refresh();

        $activityData = [
            'changed_from' => $initialInventoryData,
            'changed_to'   => ProductVariantItemService::getInventoryData(inventory: $variantItem->inventory),
            'description'  => 'Venda editada',
        ];

        ProductVariantItemService::createInventoryActivity(data: $activityData, inventory: $variantItem->inventory);

        // Update the traded item
        $currentItem->quantity = $updatedItem['quantity'];
        $currentItem->save();

        unset($this->data['traded_items'][$index]);
    }

    protected function removeTradedItemAndRestoreInventory(TradedItem $currentItem): void
    {
        $variantItem = ProductVariantItem::find($currentItem->businessable_id);

        $initialInventoryData = ProductVariantItemService::getInventoryData(inventory: $variantItem->inventory);

        $variantItem->inventory->increment('available', $currentItem->quantity);
        $variantItem->inventory->refresh();

        $activityData = [
            'changed_from' => $initialInventoryData,
            'changed_to'   => ProductVariantItemService::getInventoryData(inventory: $variantItem->inventory),
            'description'  => 'Venda editada',
        ];

        ProductVariantItemService::createInventoryActivity(data: $activityData, inventory: $variantItem->inventory);

        // Remove the traded item
        $currentItem->delete();
    }

    protected function addTradedItem(): void
    {
        foreach ($this->data['traded_items'] as $newItem) {
            $newItem['businessable_type'] = ProductVariantItem::class;
            $newItem['businessable_id'] = $newItem['product_variant_item_id'];

            $this->record->tradedItems()
                ->create($newItem);

            $variantItem = ProductVariantItem::find($newItem['product_variant_item_id']);

            $initialInventoryData = ProductVariantItemService::getInventoryData($variantItem->inventory);

            $variantItem->inventory->decrement('available', $newItem['quantity']);
            $variantItem->inventory->refresh();

            $activityData = [
                'changed_from' => $initialInventoryData,
                'changed_to'   => ProductVariantItemService::getInventoryData($variantItem->inventory),
                'description'  => 'Venda editada',
            ];

            ProductVariantItemService::createInventoryActivity($activityData, $variantItem->inventory);
        }
    }
}
