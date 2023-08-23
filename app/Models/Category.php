<?php

namespace App\Models;

use App\Enums\CategoryRole;
use App\Enums\DefaultStatus;
use App\Models\Cms\Post as CmsPost;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'role',
        'name',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'order',
        'status'
    ];

    /**
     * Get all of the cms posts that are assigned this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function cmsPosts(): MorphToMany
    {
        return $this->morphedByMany(CmsPost::class, 'categorable', null, 'category_id');
    }

    /**
     * The main category of the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(Self::class, 'category_id');
    }

    /**
     * The sub categories that belong to the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(Self::class, 'category_id');
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
            ? CategoryRole::getDescription((int) $this->role)
            : null;
    }

    public function getDisplayStatusAttribute(): string
    {
        return DefaultStatus::getDescription((int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return DefaultStatus::getColorByValue((int) $this->status);
    }
}
