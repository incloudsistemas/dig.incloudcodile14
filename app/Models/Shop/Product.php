<?php

namespace App\Models\Shop;

use App\Casts\DateTimeCast;
use App\Traits\Cms\Postable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, Postable;

    protected $table = 'shop_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'subtitle',
        'excerpt',
        'body',
        'embed_video',
        'tags',
        'has_variants',
        'publish_on',
        'order',
        'featured',
        'comment',
        'publish_at',
        'expiration_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags'          => 'array',
        'has_variants'  => 'boolean',
        'publish_on'    => 'array',
        'featured'      => 'boolean',
        'comment'       => 'boolean',
        'publish_at'    => DateTimeCast::class,
        'expiration_at' => DateTimeCast::class,
    ];

    /**
     * The variant items that belong to the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variantItems(): HasMany
    {
        return $this->hasMany(related: ProductVariantItem::class, foreignKey: 'product_id');
    }

    /**
     * The variant options that belong to the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variantOptions(): HasMany
    {
        return $this->hasMany(related: ProductVariantOption::class, foreignKey: 'product_id');
    }

    /**
     * The brand of the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productBrand(): BelongsTo
    {
        return $this->belongsTo(related: ProductBrand::class, foreignKey: 'brand_id');
    }

    /**
     * The category of the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(related: ProductCategory::class, foreignKey: 'category_id');
    }

    /**
     * EVENT LISTENERS.
     *
     */

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Product $product): void {
            $product->slug = $product->slug . '//deleted_' . md5(uniqid());
            $product->save();

            // Removing relations
            $product->variantOptions()->delete();

            foreach ($product->variantItems as $variantItem) {
                $variantItem->delete();
            }
        });
    }

    /**
     * SCOPES.
     *
     */

    /**
     * MUTATORS.
     *
     */

    /**
     * CUSTOMS.
     *
     */
}
