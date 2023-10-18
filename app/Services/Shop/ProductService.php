<?php

namespace App\Services\Shop;

use App\Models\Shop\Product;
use DateTime;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductService
{
    public function __construct(protected Product $product)
    {
        $this->product = $product;
    }

    public function tableFilterByCreatedAt(Builder $query, array $data): Builder
    {
        return $query
            ->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
            )
            ->when(
                $data['created_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
            );
    }

    // public function tableSearchByCreatedAt(Builder $query, string $search): Builder
    // {
    //     if (!$search || strlen($search) !== 10) {
    //         return $query;
    //     }

    //     $formattedDate = DateTime::createFromFormat('d/m/Y', $search);

    //     if (!$formattedDate) {
    //         return $query;
    //     }

    //     return $query->whereDate('created_at', $formattedDate->format('Y-m-d'));
    // }

    public function tableSearchBySku(Builder $query, string $search): Builder
    {
        return $query->whereHas('variantItems', function (Builder $query) use ($search): Builder {
            return $query->where('sku', 'like', "%{$search}%");
        });
    }

    // public function tableSortBySku(Builder $query, string $direction): Builder
    // {
    //     return $query->join('shop_product_variant_items as variant_items', 'shop_products.id', '=', 'variant_items.product_id')
    //         ->orderBy('variant_items.sku', $direction)
    //         ->select('shop_products.*');
    // }

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
