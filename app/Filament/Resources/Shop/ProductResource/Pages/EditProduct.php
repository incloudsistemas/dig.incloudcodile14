<?php

namespace App\Filament\Resources\Shop\ProductResource\Pages;

use App\Filament\Resources\Shop\ProductResource;
use App\Services\Shop\ProductService;
use App\Services\Shop\ProductVariantItemService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!$this->record->has_variants) {
            $defaultVariant = $this->record->variantItems[0];

            $data['default_variant']['price'] = $defaultVariant->display_price;
            $data['default_variant']['compare_at_price'] = $defaultVariant->display_compare_at_price;
            $data['default_variant']['unit_cost'] = $defaultVariant->display_unit_cost;
            $data['profit'] = $defaultVariant->display_profit;
            $data['profit_margin'] = $defaultVariant->display_profit_margin;

            $data['default_variant']['sku'] = $defaultVariant->sku;
            $data['default_variant']['barcode'] = $defaultVariant->barcode;
            $data['default_variant']['inventory_management'] = $defaultVariant->inventory_management;
            $data['default_variant']['inventory_out_allowed'] = $defaultVariant->inventory_out_allowed;
            // $data['default_variant']['inventory_quantity'] = $defaultVariant->inventory_quantity;
            $data['default_variant']['inventory_security_alert'] = $defaultVariant->inventory_security_alert;

            $data['default_variant']['inventory'] = ProductVariantItemService::getInventoryData(inventory: $defaultVariant->inventory);

            $data['default_variant']['requires_shipping'] = $defaultVariant->requires_shipping;
            $data['default_variant']['weight'] = $defaultVariant->weight;
            $data['default_variant']['dimensions']['height'] = $defaultVariant->dimensions['height'];
            $data['default_variant']['dimensions']['width'] = $defaultVariant->dimensions['width'];
            $data['default_variant']['dimensions']['length'] = $defaultVariant->dimensions['length'];
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (isset($data['has_variants']) && $data['has_variants'] && !$record->has_variants) {
            // Delete default variant option
            $record->variantOptions()
                ->first()
                ->delete();

            // Delete default variant item
            $record->variantItems()
                ->first()
                ->delete();
        }

        $record->update($data);

        if ($record->has_variants) {
            $this->syncVariants();
        } else {
            $this->updateDefaultVariant($data['default_variant']);
        }

        return $record;
    }

    protected function syncVariants(): void
    {
        $variants = $this->record->variantOptions
            ->whereNull('deleted_at')
            ->pluck('option_values')
            ->toArray();

        $variantCombinations = ProductService::combineVariants($variants);

        $variantItemRecords = [];
        foreach ($variantCombinations as $combination) {
            $options = array_map(function ($value) {
                return ["name" => $value];
            }, $combination);

            $variantItemRecords[] = [
                'name'    => implode(' / ', $combination),
                'options' => $options,
            ];
        }

        $variantNames = array_column($variantItemRecords, 'name');

        // Delete variants not found in array
        $this->record->variantItems()
            ->whereNotIn('name', $variantNames)
            ->each(function ($variantItem) {
                $variantItem->delete();
            });

        // Existing and new variants
        $existingVariantNames = $this->record->variantItems
            ->pluck('name')
            ->toArray();

        $newVariants = array_filter($variantItemRecords, function ($variant) use ($existingVariantNames) {
            return !in_array($variant['name'], $existingVariantNames);
        });

        $this->record->variantItems()
            ->createMany($newVariants);
    }

    protected function updateDefaultVariant(array $defaultVariant): void
    {
        $variantItem = $this->record->variantItems()
            ->first();

        if ($variantItem) {
            $variantItem->update($defaultVariant);

            if (isset($defaultVariant['inventory'])) {
                $data['activity']['changed_from'] = ProductVariantItemService::getInventoryData(inventory: $variantItem->inventory);
                $data['activity']['changed_to'] = $defaultVariant['inventory'];

                // Update variant inventory
                $variantItem->inventory()
                    ->update($defaultVariant['inventory']);

                ProductVariantItemService::createInventoryActivity(data: $data['activity'], inventory: $variantItem->inventory);
            }
        }
    }
}
