<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductInventory extends Model
{
    use HasFactory;

    protected $table = 'shop_product_inventories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'variant_item_id',
        'available',
        'committed',
        'unavailable_damaged',
        'unavailable_quality_control',
        'unavailable_safety',
        'unavailable_other',
        'to_receive'
    ];

    /**
     * The activities that belong to the inventory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryActivities(): HasMany
    {
        return $this->hasMany(related: InventoryActivity::class, foreignKey: 'inventory_id');
    }

    /**
     * The variant item of the variant inventory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function variantItem(): BelongsTo
    {
        return $this->belongsTo(related: ProductVariantItem::class, foreignKey: 'variant_item_id');
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
    public function getTotalAttribute(): int
    {
        $total = $this->available + $this->committed + $this->unavailable_damaged + $this->unavailable_quality_control + $this->unavailable_safety + $this->unavailable_other;
        return $total;
    }
}
