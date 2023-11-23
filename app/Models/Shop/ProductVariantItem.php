<?php

namespace App\Models\Shop;

use App\Casts\FloatCast;
use App\Enums\DefaultStatus;
use App\Services\Shop\ProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariantItem extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'shop_product_variant_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'name',
        'images',
        'options',
        'price',
        'compare_at_price',
        'unit_cost',
        'sku',
        'barcode',
        'inventory_management',
        'inventory_out_allowed',
        // 'inventory_quantity',
        'inventory_security_alert',
        'requires_shipping',
        'weight',
        'dimensions',
        'order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images'                => 'array',
        'options'               => 'array',
        'price'                 => FloatCast::class,
        'compare_at_price'      => FloatCast::class,
        'unit_cost'             => FloatCast::class,
        'inventory_management'  => 'boolean',
        'inventory_out_allowed' => 'boolean',
        'requires_shipping'     => 'boolean',
        'weight'                => FloatCast::class,
        'dimensions'            => 'array',
    ];

    /**
     * The inventory of the variant option.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(related: ProductInventory::class, foreignKey: 'variant_item_id');
    }

    /**
     * The product of the variant option.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(related: Product::class, foreignKey: 'product_id');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 345, 230)
            ->nonQueued();
    }

    /**
     * EVENT LISTENERS.
     *
     */

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Self $variantItem): void {
            $variantItem->sku = $variantItem->sku . '//deleted_' . md5(uniqid());
            $variantItem->barcode = $variantItem->barcode . '//deleted_' . md5(uniqid());
            $variantItem->save();
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

    public function getDisplayInventoryTrackedAttribute(): string
    {
        return (int) $this->inventory_tracked === 1
            ? 'Sim'
            : 'Não';
    }

    public function getDisplayInventoryOutAllowedAttribute(): string
    {
        return (int) $this->inventory_out_allowed === 1
            ? 'Sim'
            : 'Não';
    }

    public function getDisplayRequiresShippingAttribute(): string
    {
        return (int) $this->requires_shipping === 1
            ? 'Sim'
            : 'Não';
    }

    public function getDisplayNameAttribute(): string
    {
        $displayName = $this->product->name;

        if ($this->name !== 'Default Variant') {
            $displayName .= " - {$this->name}";
        }

        return "{$displayName}";
    }

    public function getDisplayNameWithSkuAttribute(): string
    {
        $displayName = $this->product->name;

        if ($this->name !== 'Default Variant') {
            $displayName .= " - {$this->name}";
        }

        return "{$displayName} ({$this->sku})";
    }

    public function getDisplayStatusAttribute(): string
    {
        return DefaultStatus::getDescription(value: (int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return DefaultStatus::getColorByValue(status: (int) $this->status);
    }

    public function getDisplayPriceAttribute(): ?string
    {
        return $this->price ? number_format($this->price, 2, ',', '.') : null;
    }

    public function getDisplayCompareAtPriceAttribute(): ?string
    {
        return $this->compare_at_price ? number_format($this->compare_at_price, 2, ',', '.') : null;
    }

    public function getDisplayUnitCostAttribute(): ?string
    {
        return $this->unit_cost ? number_format($this->unit_cost, 2, ',', '.') : null;
    }

    public function getDisplayProfitAttribute(): ?string
    {
        if (!$this->price || !$this->unit_cost) {
            return null;
        }

        $profit = $this->price - $this->unit_cost;

        return number_format($profit, 2, ',', '.');
    }

    public function getDisplayProfitMarginAttribute(): ?string
    {
        if (!$this->price || !$this->unit_cost) {
            return null;
        }

        $profit = $this->price - $this->unit_cost;
        $profitMargin = ($profit / $this->price) * 100;
        $profitMargin = round(floatval($profitMargin), precision: 2);

        return number_format($profitMargin, 2, ',', '.');
    }
}
