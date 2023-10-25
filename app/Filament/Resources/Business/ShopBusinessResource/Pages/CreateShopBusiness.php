<?php

namespace App\Filament\Resources\Business\ShopBusinessResource\Pages;

use App\Filament\Resources\Business\ShopBusinessResource;
use App\Models\Business\ShopBusiness;
use App\Models\Shop\ProductVariantItem;
use App\Services\Shop\ProductVariantItemService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Relations\Relation;

class CreateShopBusiness extends CreateRecord
{
    protected static string $resource = ShopBusinessResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }

    protected function afterCreate(): void
    {
        $this->createFunnelStage();
        $this->createTradedItemsAndUpdateInventory();
        $this->updateContactRolesToCustomer();
    }

    protected function createFunnelStage(): void
    {
        $data['funnel_stages']['funnel_stage_id'] = $this->data['funnel_stage_id'];

        $morphMap  = Relation::morphMap();
        $modelType = array_search(get_class($this->record), $morphMap, true);

        $this->record->funnelStages()->updateOrCreate(
            ['model_type' => $modelType, 'model_id' => $this->record->id, 'funnel_id' => $this->data['funnel_id']],
            $data['funnel_stages']
        );
    }

    protected function createTradedItemsAndUpdateInventory(): void
    {
        foreach ($this->data['traded_items'] as $tradedItem) {
            $this->createTradedItem(tradedItem: $tradedItem);
            $this->updateInventoryAndCreateActivity(tradedItem: $tradedItem);
        }
    }

    protected function createTradedItem(array $tradedItem): void
    {
        $morphMap  = Relation::morphMap();
        $modelType = array_search(ProductVariantItem::class, $morphMap, true);

        $tradedItem['businessable_type'] = $modelType;
        $tradedItem['businessable_id'] = $tradedItem['product_variant_item_id'];

        $this->record->tradedItems()
            ->create($tradedItem);
    }

    protected function updateInventoryAndCreateActivity(array $tradedItem): void
    {
        $variantItem = ProductVariantItem::find($tradedItem['product_variant_item_id']);

        if (!$variantItem) {
            return;
        }

        $initialInventoryData = ProductVariantItemService::getInventoryData($variantItem->inventory);

        $variantItem->inventory->decrement('available', $tradedItem['quantity']);
        $variantItem->inventory->refresh();

        $funnelStage = $this->record->funnelStages()
            ->where('funnel_stage_id', $this->data['funnel_stage_id'])
            ->first();

        $activityData = [
            'changed_from' => $initialInventoryData,
            'changed_to'   => ProductVariantItemService::getInventoryData($variantItem->inventory),
            'description'  => $funnelStage->name ?? 'Venda realizada',
        ];

        ProductVariantItemService::createInventoryActivity($activityData, $variantItem->inventory);
    }

    protected function updateContactRolesToCustomer(): void
    {
        $contact = $this->record->contact;
        $roleId  = 3; // Cliente/Customer

        if (!$contact->roles->contains($roleId)) {
            $contact->roles()->attach($roleId);
        }
    }
}
