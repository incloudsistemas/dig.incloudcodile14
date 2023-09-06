<?php

namespace App\Models\Cms;

use App\Casts\DateTimeCast;
use App\Enums\Cms\DefaultPostStatus;
use App\Enums\Cms\PostSliderRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostSlider extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'cms_post_sliders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slideable_type',
        'slideable_id',
        'role',
        'title',
        'subtitle',
        'body',
        'cta',
        'embed_video',
        'order',
        'status',
        'settings',
        'publish_at',
        'expiration_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cta' => 'array',
        'settings' => 'array',
        'publish_at' => DateTimeCast::class,
        'expiration_at' => DateTimeCast::class,
    ];

    /**
     * Get all of the owning slideable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function slideable(): MorphTo
    {
        return $this->morphTo();
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

    public function scopeBySlideableTypes(Builder $query, array $slideableTypes): Builder
    {
        return $query->whereIn('slideable_type', $slideableTypes);
    }

    public function scopeBySlideableTypesAndIds(Builder $query, array $slideableTypes, array $slideableIds): Builder
    {
        return $query->bySlideableTypes(slideableTypes: $slideableTypes)
            ->whereIn('slideable_id', $slideableIds);
    }

    /**
     * MUTATORS.
     *
     */

    /**
     * CUSTOMS.
     *
     */

    public function getDisplayRoleAttribute(): string
    {
        return isset($this->role)
            ? PostSliderRole::getDescription(value: (int) $this->role)
            : null;
    }

    public function getDisplayStatusAttribute(): string
    {
        return DefaultPostStatus::getDescription(value: (int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return DefaultPostStatus::getColorByValue(status: (int) $this->status);
    }
}
