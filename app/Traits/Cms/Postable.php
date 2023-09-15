<?php

namespace App\Traits\Cms;

use App\Models\Cms\Post;
use App\Models\Cms\PostSlider;
use App\Models\Cms\PostSubcontent;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
    public function postCategories(): BelongsToMany
    {
        return $this->cmsPost?->postCategories();
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

    /**
     * WEBSITE EXCLUSIVE.
     *
     */

    /**
     * Base query for filtering posts by their status and dates.
     *
     * @param array $statuses
     * @param string $orderBy
     * @param string $direction
     * @param string $publishAtDirection
     * @return Builder
     */
    protected function baseWebQuery(
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->newQuery()
            ->with(['cmsPost', 'cmsPost.owner', 'cmsPost.categories'])
            ->whereHas('cmsPost', fn (Builder $query): Builder => $query->whereIn('status', $statuses))
            ->where('publish_at', '<=', now())
            ->where(fn (Builder $query): Builder => $query->where('expiration_at', '>', now())
                ->orWhereNull('expiration_at'))
            ->orderBy($orderBy, $direction)
            ->orderBy('publish_at', $publishAtDirection);
    }

    public function getWeb(
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->baseWebQuery(
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        );
    }

    public function getWebFeatured(
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWeb(
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->where('featured', 1);
    }

    public function getWebByRoles(
        array $roles,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWeb(
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->whereIn('role', $roles);
    }

    public function getWebFeaturedByRoles(
        array $roles,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWebFeatured(
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->whereIn('role', $roles);
    }

    public function findWebBySlug(
        string $slug,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWeb(
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->where('slug', $slug);
    }

    public function searchWeb(
        string $keyword,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWeb(
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->where('title', 'like', '%' . $keyword . '%');
    }

    public function searchWebByRoles(
        string $keyword,
        array $roles,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->searchWeb(
            keyword: $keyword,
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->whereIn('role', $roles);
    }

    public function getWebByCategory(
        string $categorySlug,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWeb(
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->whereHas('cmsPost.categories', function (Builder $query) use ($categorySlug): Builder {
                return $query->where('slug', $categorySlug);
            });
    }

    public function getWebByCategoryAndRoles(
        string $categorySlug,
        array $roles,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWebByCategory(
            categorySlug: $categorySlug,
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->whereIn('role', $roles);
    }

    public function getWebByRelatedCategories(
        array $categoryIds,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWeb(
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->whereHas('cmsPost.categories', function (Builder $query) use ($categoryIds): Builder {
                return $query->whereIn('id', $categoryIds);
            });
    }

    public function getWebByRelatedCategoriesAndRoles(
        array $categoryIds,
        array $roles,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc',
        string $publishAtDirection = 'desc'
    ): Builder {
        return $this->getWebByRelatedCategories(
            categoryIds: $categoryIds,
            statuses: $statuses,
            orderBy: $orderBy,
            direction: $direction,
            publishAtDirection: $publishAtDirection
        )
            ->whereIn('role', $roles);
    }

    public function getWebSliders(
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc'
    ): Builder {
        return $this->sliders()
            ->whereIn('status', $statuses)
            ->where('publish_at', '<=', now())
            ->where(fn (Builder $query): Builder => $query->where('expiration_at', '>', now())
                ->orWhereNull('expiration_at'))
            ->orderBy($orderBy, $direction)
            ->orderBy('publish_at', 'desc');
    }

    public function getWebSubcontentsByRoles(
        array $roles,
        array $statuses = [1,],
        string $orderBy = 'order',
        string $direction = 'desc'
    ): Builder {
        return $this->subcontents()
            ->whereIn('role', $roles)
            ->whereIn('status', $statuses)
            ->where('publish_at', '<=', now())
            ->where(fn (Builder $query): Builder => $query->where('expiration_at', '>', now())
                ->orWhereNull('expiration_at'))
            ->orderBy($orderBy, $direction)
            ->orderBy('publish_at', 'desc');
    }
}
