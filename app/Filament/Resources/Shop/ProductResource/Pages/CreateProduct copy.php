<?php

namespace App\Filament\Resources\Shop\ProductResource\Pages;

use App\Filament\Resources\Shop\ProductResource;
use App\Services\Shop\ProductService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $record = static::getModel()::create($data);

            $record->cmsPost()
                ->create(['publish_at' => now()]);

            if (!$data['has_variants']) {
                $this->createDefaultVariant(record: $record, data: $data);
            }

            return $record;
        });
    }

    protected function afterCreate(): void
    {
        DB::transaction(function (): void {
            $data = $this->form->getState();

            if ($this->record->has_variants) {
                $this->createVariantItems(data: $data);
            }
        });
    }

    protected function createDefaultVariant(Model $record, array $data): void
    {
        $defaultVariantOption = $record->variantOptions()
            ->create([
                'name'          => 'Default Variant',
                'option_values' => [['name' => 'Default']]
            ]);

        $data['default_variant']['name'] = 'Default Variant';
        $data['default_variant']['options'] = $defaultVariantOption->option_values;

        $record->variantItems()
            ->create($data['default_variant']);
    }

    protected function createVariantItems(array $data): void
    {
        $variants = $this->record->variantOptions
            ->pluck('option_values')
            ->toArray();

        $variantCombinations = ProductService::combineVariants($variants);

        $variantItemRecords = [];
        $key = 1;
        foreach ($variantCombinations as $combination) {
            $options = array_map(function ($value) {
                return ["name" => $value];
            }, $combination);

            $data['default_variant']['sku'] = $this->incrementSku(key: $key, sku: $data['default_variant']['sku']);

            $combinationRecord = [
                'name'    => implode(' / ', $combination),
                'options' => $options,
            ];

            $combinationRecord = array_merge($combinationRecord, $data['default_variant']);
            $variantItemRecords[] = $combinationRecord;
            $key++;

            // Reset next data
            $data['default_variant']['barcode'] = null;
            $data['default_variant']['inventory_quantity'] = null;
        }

        $this->record->variantItems()
            ->createMany($variantItemRecords);
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
