<?php

namespace App\Traits\Cms;

use App\Models\Cms\Post;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

trait Postable
{
    use SoftDeletes;    

    /**
     * The categories that belongs to the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function categories(): Relation
    {
        return $this->cmsPost?->categories();
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
