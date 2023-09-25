<?php

namespace App\Http\Controllers\Web;

use App\Models\Cms\BlogPost;
use App\Models\Cms\Page;
use App\Models\Cms\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class BlogPostController extends Controller
{
    protected $idxPage = 'blog';
    protected $paginateNum = 8;

    public function __construct(protected Page $page, protected BlogPost $blog, protected PostCategory $category)
    {
        parent::__construct($page);

        $this->blog = $blog;
        $this->category = $category;

        // $blogCategories = $this->category->getWebPostCategoriesByPostableTypes(['cms-blog', ])
        //     ->get();

        // View::share('blogCategories', $blogCategories);
    }

    public function index()
    {
        $page = $this->getPage(slug: $this->idxPage);
        $this->generateSEOAttribute(page: $page);

        $blogPosts = null;

        return view('web.blog.index', compact('page', 'blogPosts'));
    }

    public function show($slug)
    {
        $page = $this->blog->findWebBySlug(slug: $slug, statuses: $this->getPostStatusByUser())
            ->firstOrFail();
        $this->generateSEOAttribute(page: $page);

        $idxPage = $this->getPage(slug: $this->idxPage);

        return view('web.blog.index', compact('page', 'idxPage'));
    }

    public function indexByCategory($category)
    {
        $page = $this->getPage(slug: $this->idxPage);
        $this->generateSEOAttribute(page: $page);

        $blogPosts = null;

        return view('web.blog.index', compact('page', 'blogPosts'));
    }

    public function indexByRole($role)
    {
        $page = $this->getPage(slug: $this->idxPage);
        $this->generateSEOAttribute(page: $page);

        $blogPosts = null;

        return view('web.blog.index', compact('page', 'blogPosts'));
    }

    public function search(Request $request)
    {
        $page = $this->getPage(slug: $this->idxPage);
        $this->generateSEOAttribute(page: $page);

        $data = $request->all();

        $blogPosts = null;

        return view('web.blog.index', compact('page', 'data', 'blogPosts'));
    }
}
