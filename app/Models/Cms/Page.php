<?php

namespace App\Models\Cms;

use App\Casts\DateTimeCast;
use App\Traits\Cms\Postable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

class Page extends Model implements HasMedia
{
    use HasFactory, Postable;

    protected $table = 'cms_pages';

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
        'order',
        'featured',
        'comment',
        'publish_at',
        'expiration_at',        
        'settings'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cta' => 'array',
        'featured' => 'boolean',
        'comment' => 'boolean',
        'publish_at' => DateTimeCast::class,
        'expiration_at' => DateTimeCast::class,
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
}
