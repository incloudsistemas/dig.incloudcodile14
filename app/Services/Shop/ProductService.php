<?php

namespace App\Services\Shop;

use App\Models\Shop\Product;

class ProductService
{
    public function __construct(protected Product $product)
    {
        $this->product = $product;
    }

    public static function combineVariants(array $variantLists): array
    {
        $result = array(array());
        foreach ($variantLists as $list) {
            $newResult = [];
            foreach ($result as $combined) {
                foreach ($list as $item) {
                    $newCombined = array_merge($combined, [$item['name']]);
                    $newResult[] = $newCombined;
                }
            }

            $result = $newResult;
        }

        return $result;
    }

    public function anonymizeUniqueSlugWhenDeleted(Product $product): void
    {
        $product->slug = $product->slug . '//deleted_' . md5(uniqid());
        $product->save();
    }
}
