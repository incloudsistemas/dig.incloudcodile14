<?php

namespace App\Models\Shop;

use App\Enums\DefaultStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shop_product_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'status'
    ];

    /**
     * The shop products that belong to the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(related: Product::class, foreignKey: 'category_id');
    }

    /**
     * The main category of the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(related: Self::class, foreignKey: 'category_id');
    }

    /**
     * The sub categories that belong to the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(related: Self::class, foreignKey: 'category_id');
    }

    /**
     * SCOPES.
     *
     */

    public function scopeByStatuses(Builder $query, array $statuses = [1,]): Builder
    {
        return $query->whereIn('status', $statuses);
    }

    /**
     * MUTATORS.
     *
     */

    /**
     * CUSTOMS.
     *
     */

    public function getDisplayStatusAttribute(): string
    {
        return DefaultStatus::getDescription(value: (int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return DefaultStatus::getColorByValue(status: (int) $this->status);
    }
}
