<?php

namespace App\Models\Cms;

use App\Casts\DateTimeCast;
use App\Enums\Cms\BlogRole;
use App\Traits\Cms\Postable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class BlogPost extends Model implements HasMedia
{
    use HasFactory, Postable;

    protected $table = 'cms_blog_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'body',
        'url',
        'embed_video',
        'tags',
        'order',
        'featured',
        'comment',
        'publish_at',
        'expiration_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags'          => 'array',
        'featured'      => 'boolean',
        'comment'       => 'boolean',
        'publish_at'    => DateTimeCast::class,
        'expiration_at' => DateTimeCast::class,
    ];

    /**
     * EVENT LISTENERS.
     *
     */

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Self $post): void {
            $post->slug = $post->slug . '//deleted_' . md5(uniqid());
            $post->save();
        });
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
            ? BlogRole::getDescription(value: (int) $this->role)
            : null;
    }
}
