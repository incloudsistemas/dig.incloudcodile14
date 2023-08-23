<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Page extends Model
{
    use HasFactory;

    protected $table = 'cms_pages';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page_id',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'body',
        'cta',
        'url',
        'embed_video',
        'comment',
        'settings'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cta' => 'array',
        'comment' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * The main page of the page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainPage(): BelongsTo
    {
        return $this->belongsTo(Self::class, 'page_id');
    }

    /**
     * The sub pages that belong to the page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subpages(): HasMany
    {
        return $this->hasMany(Self::class, 'page_id');
    }

    /**
     * Get the page's post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function cmsPost(): MorphOne
    {
        return $this->morphOne(Post::class, 'postable');
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

    public function getDisplayCommentAttribute(): string
    {
        return isset($this->comment) && (int) $this->comment === 1
            ? 'Sim'
            : 'Não';
    }
}
