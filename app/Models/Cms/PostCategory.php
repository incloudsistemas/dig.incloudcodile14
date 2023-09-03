<?php

namespace App\Models\Cms;

use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostCategory extends Model
{
    use HasFactory;

    protected $table = 'cms_post_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'order',
        'status'
    ];

    /**
     * The posts that belongs to the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cmsPosts(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Post::class, 
            table: 'cms_post_has_categories', 
            foreignPivotKey: 'category_id', 
            relatedPivotKey: 'post_id'
        );
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
