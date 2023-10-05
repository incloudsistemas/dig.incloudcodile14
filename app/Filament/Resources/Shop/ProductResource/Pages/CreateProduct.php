<?php

namespace App\Filament\Resources\Shop\ProductResource\Pages;

use App\Filament\Resources\Shop\ProductResource;
use App\Models\Shop\ProductVariantItem;
use App\Services\Shop\ProductService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        if (!$data['has_variants']) {
            $this->createDefaultVariant(record: $record, data: $data);
        }

        Session::put('default_variant', $data['default_variant']);

        return $record;
    }

    protected function afterCreate(): void
    {
        //  Create the variant items, if have then
        if ($this->record->has_variants) {
            $defaultVariant = Session::get('default_variant');
            $this->createVariantItems(record: $this->record, defaultVariant: $defaultVariant);
            $this->createVariantItemsInventories(record: $this->record, defaultVariant: $defaultVariant);
        }

        Session::forget('default_variant');

        // Force post create
        if (!$this->record->cmsPost) {
            $this->record->cmsPost()
                ->create(['publish_at' => now()]);
        }
    }

    protected function createDefaultVariant(Model $record, array $data): void
    {
        // Create the default variant option
        $defaultVariantOption = $record->variantOptions()
            ->create([
                'name'          => 'Default Variant',
                'option_values' => [['name' => 'Default']]
            ]);

        $data['default_variant']['name'] = 'Default Variant';
        $data['default_variant']['options'] = $defaultVariantOption->option_values;

        // Create the default variant item
        $variantItem = $record->variantItems()
            ->create($data['default_variant']);

        // Create variant inventory
        $variantItem->inventory()
            ->create([
                'available' => $data['default_variant']['inventory_quantity'] ?? 0,
            ]);
    }

    protected function createVariantItems(Model $record, array $defaultVariant): void
    {
        $variants = $record->variantOptions
            ->pluck('option_values')
            ->toArray();

        $variantCombinations = ProductService::combineVariants($variants);

        $variantItemRecords = [];
        $key = 1;
        foreach ($variantCombinations as $combination) {
            $options = array_map(function ($value) {
                return ["name" => $value];
            }, $combination);

            // Increment SKU
            $defaultVariant['sku'] = $this->incrementSku(key: $key, sku: $defaultVariant['sku']);

            $combinationRecord = [
                'name'    => implode(' / ', $combination),
                'options' => $options,
            ];

            $combinationRecord = array_merge($combinationRecord, $defaultVariant);
            $variantItemRecords[] = $combinationRecord;
            $key++;

            // Reset next datas
            $defaultVariant['barcode'] = null;
            // $defaultVariant['inventory_quantity'] = null;
        }

        $record->variantItems()
            ->createMany($variantItemRecords);
    }

    protected function createVariantItemsInventories(Model $record, array $defaultVariant): void
    {
        foreach ($record->variantItems as $variantItem) {
            $variantItem->inventory()
                ->create([
                    'available' => $defaultVariant['inventory_quantity'] ?? 0
                ]);

            $defaultVariant['inventory_quantity'] = 0;
        }
    }

    protected function incrementSku(int $key, ?string $sku): ?string
    {
        if (!$sku) {
            return null;
        }

        // Checks if the last character of the SKU is numeric
        if (is_numeric(substr($sku, -1))) {
            if ($key > 1) {
                // Find all numbers at the end of the string
                if (preg_match('/(\d+)$/', $sku, $matches)) {
                    $number = intval($matches[1]);
                    $skuBase = substr($sku, 0, -strlen($matches[1]));
                    $newNumber = str_pad($number + 1, strlen($matches[1]), '0', STR_PAD_LEFT);
                    $newSku = $skuBase . $newNumber;
                } else {
                    $newSku = $sku . '-' . $key;
                }
            }
        } else {
            $newSku = $sku . '-' . $key;
        }

        return $newSku ?? $sku;
    }
}
