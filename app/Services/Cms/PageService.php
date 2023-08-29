<?php

namespace App\Services\Cms;

use App\Models\Cms\Page;
use Illuminate\Database\Eloquent\Builder;

class PageService
{
    public function __construct(protected Page $page)
    {
        $this->page = $page;
    }

    public function getMainPages(Builder $query, Page $page): Builder 
    {
        return $query->whereNull('page_id')
            ->where('id', '<>', $page->id);
    }

    public function anonymizeUniqueSlugWhenDeleted(Page $page): void
    {
        $page->slug = $page->slug . '//deleted_' . md5(uniqid());
        $page->save();
    }
}
