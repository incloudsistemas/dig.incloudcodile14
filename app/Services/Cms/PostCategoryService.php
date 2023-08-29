<?php

namespace App\Services\Cms;

use App\Models\Cms\PostCategory;
use Illuminate\Contracts\Database\Eloquent\Builder;

class PostCategoryService
{
    public function __construct(protected PostCategory $category)
    {
        $this->category = $category;
    }

    public function forceScopeActiveStatus(): Builder
    {
        return $this->category->byStatuses(statuses: [1,]); // 1 - active
    }
}
