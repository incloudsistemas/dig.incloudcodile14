<?php

namespace App\Filament\Resources\Business\ShopBusinessResource\Pages;

use App\Filament\Resources\Business\ShopBusinessResource;
use App\Models\Business\Business;
use App\Models\Shop\ProductInventory;
use App\Models\Shop\ProductVariantItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Relations\Relation;

class EditShopBusiness extends EditRecord
{
    protected static string $resource = ShopBusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $mainFunnel = $this->record->funnels->first();

        $mainFunnelStage = $this->record->funnelStages->where('funnel_id', $mainFunnel->id)
            ->first();

        $data['funnel_id'] = $mainFunnel->id;
        $data['funnel_stage_id'] = $mainFunnelStage->funnel_stage_id ?? null;

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
        // $data['funnel_stages']['funnel_id'] = $this->data['funnel_id'];
        $data['funnel_stages']['funnel_stage_id'] = $this->data['funnel_stage_id'];

        $morphMap  = Relation::morphMap();
        $modelType = array_search(get_class($this->record), $morphMap, true);

        $this->record->funnelStages()->updateOrCreate(
            ['model_type' => $modelType, 'model_id' => $this->record->id, 'funnel_id' => $this->data['funnel_id']],
            $data['funnel_stages']
        );
    }
}
