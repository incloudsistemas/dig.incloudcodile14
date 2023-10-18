<?php

namespace App\Models\Business;

use App\Casts\FloatCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TradedItem extends Model
{
    use HasFactory;

    protected $table = 'business_traded_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'businessable_type',
        'businessable_id',
        'quantity',
        'price',
        'unit_price',
        'cost',
        'unit_cost',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price'      => FloatCast::class,
        'unit_price' => FloatCast::class,
        'cost'       => FloatCast::class,
        'unit_cost'  => FloatCast::class,
    ];

    /**
     * The user that owns the business.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(related: Business::class, foreignKey: 'business_id');
    }

    /**
     * Get all of the owning businessable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function businessable(): MorphTo
    {
        return $this->morphTo();
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

    public function getDisplayPriceAttribute(): ?string
    {
        return $this->price ? number_format($this->price, 2, ',', '.') : null;
    }

    public function getDisplayUnitPriceAttribute(): ?string
    {
        return $this->unit_price ? number_format($this->unit_price, 2, ',', '.') : null;
    }

    public function getDisplayCostAttribute(): ?string
    {
        return $this->cost ? number_format($this->cost, 2, ',', '.') : null;
    }

    public function getDisplayUnitCostAttribute(): ?string
    {
        return $this->unit_cost ? number_format($this->unit_cost, 2, ',', '.') : null;
    }
}
