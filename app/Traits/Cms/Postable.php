<?php

namespace App\Traits\Cms;

use App\Models\Cms\Post;
use App\Models\Cms\PostSlider;
use App\Models\Cms\PostSubcontent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait Postable
{
    use SoftDeletes, InteractsWithMedia;

    /**
     * Get the post's subcontents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function subcontents(): MorphMany
    {
        return $this->morphMany(PostSubcontent::class, 'contentable');
    }

    /**
     * Get the post's sliders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function sliders(): MorphMany
    {
        return $this->morphMany(PostSlider::class, 'slideable');
    }

    /**
     * The categories that belongs to the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->cmsPost?->categories();
    }

    /**
     * Get the postable's post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function cmsPost(): MorphOne
    {
        return $this->morphOne(related: Post::class, name: 'postable');
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

    public function getDisplayFeaturedAttribute(): ?string
    {
        if (!isset($this->featured)) {
            return null;
        }

        return (int) $this->featured === 1
            ? 'Sim'
            : 'Não';
    }

    public function getDisplayCommentAttribute(): ?string
    {
        if (!isset($this->comment)) {
            return null;
        }

        return (int) $this->comment === 1
            ? 'Sim'
            : 'Não';
    }
}
