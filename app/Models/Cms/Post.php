<?php

namespace App\Models\Cms;

use App\Casts\DateTimeCast;
use App\Enums\Cms\DefaultPostStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Post extends Model
{
    use HasFactory;

    protected $table = 'cms_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'postable_type',
        'postable_id',
        'user_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured',
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
        'meta_keywords' => 'array',
        'featured' => 'boolean',
        'custom' => 'array',
        'publish_at' => DateTimeCast::class,
        'expiration_at' => DateTimeCast::class,
    ];

    /**
     * The categories that belongs to the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            related: PostCategory::class, 
            table: 'cms_post_has_categories', 
            foreignPivotKey: 'post_id', 
            relatedPivotKey: 'category_id'
        );
    }

    /**
     * The user that owns the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'user_id');
    }

    /**
     * Get all of the owning postable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function postable(): MorphTo
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

    public function getDisplayFeaturedAttribute(): string
    {
        return isset($this->featured) && (int) $this?->featured === 1
            ? 'Sim'
            : 'Não';
    }

    public function getDisplayStatusAttribute(): string
    {
        return DefaultPostStatus::getDescription((int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return DefaultPostStatus::getColorByValue((int) $this->status);
    }

    public function getDisplayPublishAtAttribute(): string
    {
        return $this->publish_at?->format('d/m/Y H:i');
    }

    public function getDisplayExpirationAtAttribute(): string
    {
        return $this->expiration_at?->format('d/m/Y H:i');
    }
}
