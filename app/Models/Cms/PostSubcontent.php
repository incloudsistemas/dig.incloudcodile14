<?php

namespace App\Models\Cms;

use App\Casts\DateTimeCast;
use App\Enums\Cms\DefaultPostStatus;
use App\Enums\Cms\PostSubcontentRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostSubcontent extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'cms_post_subcontents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contentable_type',
        'contentable_id',
        'role',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'body',
        'cta',
        'embed_video',
        'order',
        'status',
        'custom',
        'publish_at',
        'expiration_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cta'           => 'array',
        'custom'        => 'array',
        'publish_at'    => DateTimeCast::class,
        'expiration_at' => DateTimeCast::class,
    ];

    /**
     * Get all of the owning contentable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function contentable(): MorphTo
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
            ? PostSubcontentRole::getDescription(value: (int) $this->role)
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
