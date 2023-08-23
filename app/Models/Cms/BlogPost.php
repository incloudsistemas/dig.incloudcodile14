<?php

namespace App\Models\Cms;

use App\Enums\Cms\BlogRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BlogPost extends Model
{
    use HasFactory;

    protected $table = 'cms_blog_posts';

    public $timestamps = false;

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
        'comment'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'comment' => 'boolean',
    ];

    /**
     * Get the blog's post.
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

    public function getDisplayRoleAttribute(): string
    {
        return isset($this->role)
            ? BlogRole::getValue((int) $this->role)
            : null;
    }

    public function getDisplayCommentAttribute(): string
    {
        return isset($this->comment) && (int) $this->comment === 1
            ? 'Sim'
            : 'Não';
    }
}
