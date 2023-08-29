<?php

namespace App\Models\Cms;

use App\Models\User;
use App\Traits\Cms\Postable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory, Postable;

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
        return $this->belongsTo(related: Self::class, foreignKey: 'page_id');
    }

    /**
     * The sub pages that belong to the page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subpages(): HasMany
    {
        return $this->hasMany(related: Self::class, foreignKey: 'page_id');
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
