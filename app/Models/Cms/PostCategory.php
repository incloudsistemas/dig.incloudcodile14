<?php

namespace App\Models\Cms;

use App\Enums\DefaultStatus;
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
        return $this->belongsToMany(Post::class, 'cms_post_has_categories', 'category_id', 'post_id');
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
 
     public function getDisplayStatusAttribute(): string
     {
         return DefaultStatus::getDescription((int) $this->status);
     }
 
     public function getDisplayStatusColorAttribute(): string
     {
         return DefaultStatus::getColorByValue((int) $this->status);
     }
}
