<?php

namespace App\Models\Shop;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductBrand extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'shop_product_brands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'status'
    ];

    /**
     * The shop products that belong to the brand.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(related: Product::class, foreignKey: 'brand_id');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 345, 230)
            ->nonQueued();
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
