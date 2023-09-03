<?php

namespace App\Services\Cms;

use App\Models\Cms\Page;
use Illuminate\Database\Eloquent\Builder;

class PageService
{
    public function __construct(protected Page $page, PostService $postService)
    {
        $this->page = $page;
        $this->postService = $postService;
    }

    public function getMainPages(Builder $query, Page $page): Builder 
    {
        return $query->whereNull('page_id')
            ->where('id', '<>', $page->id);
    }

    public function deleteSubpagesWhenDeleted(Page $page): void
    {
        if ($page->subpages->count() === 0) {
            return;
        }

        foreach ($page->subpages as $subpage) {
            $this->postService->anonymizeUniqueSlugWhenDeleted($subpage);
        }

        $page->subpages()->delete();
    }
}
