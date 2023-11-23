<?php

namespace App\Http\Controllers\Web;

use App\Enums\Cms\BlogRole;
use App\Models\Cms\BlogPost;
use App\Models\Cms\Page;
use App\Models\Cms\PostCategory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

class BlogPostController extends Controller
{
    protected $idxPage = 'blog';
    protected $paginateNum = 9;

    public function __construct(
        protected Page $page,
        protected BlogPost $blogPost,
        protected PostCategory $category
    ) {
        parent::__construct($page);

        Paginator::useBootstrap();

        $this->blogPost = $blogPost;
        $this->category = $category;

        $modelType = MorphMapByClass(model: get_class($this->blogPost));
        $blogCategories = $this->category->getWebPostCategoriesByTypes(postableTypes: [$modelType,])
            ->get();

        View::share('blogCategories', $blogCategories);
    }

    public function index(): \Illuminate\View\View
    {
        $page = $this->getPage(slug: $this->idxPage);
        $this->generateSEOAttribute(page: $page);

        $blogPosts = $this->blogPost->getWeb(statuses: $this->getPostStatusByUser())
            ->paginate($this->paginateNum);

        return view('web.blog.index', compact('page', 'blogPosts'));
    }

    public function show($slug): \Illuminate\View\View
    {
        $page = $this->blogPost->findWebBySlug(slug: $slug, statuses: $this->getPostStatusByUser())
            ->firstOrFail();

        $this->generateSEOAttribute(page: $page);

        $idxPage = $this->getPage(slug: $this->idxPage);

        // Get related posts by categories...
        $categoryIds = $page->postCategories->pluck('id')
            ->toArray();
        $relatedPosts = $this->blogPost->getWebByRelatedCategories(categoryIds: $categoryIds, statuses: $this->getPostStatusByUser(), idToAvoid: $page->id)
            ->get();

        return view('web.blog.show', compact('page', 'idxPage', 'relatedPosts'));
    }

    public function indexByCategory($category): \Illuminate\View\View
    {
        $page = $this->getPage(slug: $this->idxPage);
        $this->generateSEOAttribute(page: $page);

        $blogPosts = $this->blogPost->getWebByCategory(categorySlug: $category, statuses: $this->getPostStatusByUser())
            ->paginate($this->paginateNum);

        return view('web.blog.index', compact('page', 'blogPosts'));
    }

    public function indexByRole($role): \Illuminate\View\View
    {
        $page = $this->getPage(slug: $this->idxPage);
        $this->generateSEOAttribute(page: $page);

        $slugs = BlogRole::getSlug();
        $roles = [array_search($role, $slugs)];

        $blogPosts = $this->blogPost->getWebByRoles(roles: $roles, statuses: $this->getPostStatusByUser())
            ->paginate($this->paginateNum);

        return view('web.blog.index', compact('page', 'blogPosts'));
    }

    public function search(Request $request): \Illuminate\View\View
    {
        $page = $this->getPage(slug: $this->idxPage);
        $this->generateSEOAttribute(page: $page);

        $data = $request->all();

        $blogPosts = $this->blogPost->searchWeb(keyword: $data['keyword'], statuses: $this->getPostStatusByUser())
            ->paginate($this->paginateNum);

        return view('web.blog.index', compact('page', 'data', 'blogPosts'));
    }
}
