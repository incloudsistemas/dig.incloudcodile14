<?php

namespace App\Services\Cms;

use App\Enums\Cms\DefaultPostStatus;
use App\Models\Cms\Page;
use App\Models\Cms\Post;
use App\Models\Cms\PostCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class PostService
{
    public function __construct(protected Post $post)
    {
        $this->post = $post;
    }

    public function tableSearchByStatus(Builder $query, string $search): Builder
    {
        $statuses = DefaultPostStatus::asSelectArray();
        $matchingStatuses = [];

        foreach ($statuses as $index => $status) {
            if (stripos($status, $search) !== false) {
                $matchingStatuses[] = $index;
            }
        }

        if ($matchingStatuses) {
            return $query->whereHas('cmsPost', function (Builder $query) use ($matchingStatuses): Builder {
                return $query->whereIn('status', $matchingStatuses);
            });
        }

        return $query;
    }

    public function tableSortByStatus(string $postableType, Builder $query, string $direction): Builder
    {
        $statuses = DefaultPostStatus::asSelectArray();
        $caseParts = [];
        $bindings = [];

        foreach ($statuses as $key => $status) {
            $caseParts[] = "WHEN (SELECT status FROM cms_posts WHERE cms_posts.postable_type = '{$postableType}' AND cms_posts.postable_id = {$postableType}.id) = ? THEN ?";
            $bindings[] = $key;
            $bindings[] = $status;
        }

        $orderByCase = "CASE " . implode(' ', $caseParts) . " END";

        return $query->selectRaw("*, ({$orderByCase}) as display_status", $bindings)
            ->orderBy('display_status', $direction);
    }

    public function tableDefaultSort(string $postableType, Builder $query): Builder
    {
        return Page::join('cms_posts', function (JoinClause $join) use ($postableType): JoinClause {
            return $join->on($postableType . '.id', '=', 'cms_posts.postable_id')
                ->where('cms_posts.postable_type', $postableType);
        })
            ->orderBy('cms_posts.order', 'desc')
            ->orderBy('cms_posts.publish_at', 'desc');
    }

    public function tableFilterGetOptionsByCategories(string $postableType): array
    {
        // statuses 1 - active
        return PostCategory::byStatuses(statuses: [1,])
            ->whereHas('cmsPosts', function (Builder $query) use ($postableType): Builder {
                return $query->where('postable_type', $postableType);
            })
            ->pluck('name', 'id')
            ->toArray();
    }

    public function tableFilterGetQueryByCategories(Builder $query, array $data): Builder
    {
        if (!$data['values'] || empty($data['values'])) {
            return $query;
        }

        return $query->whereHas('cmsPost', function (Builder $query) use ($data): Builder {
            return $query->whereHas('categories', function (Builder $query) use ($data): Builder {
                return $query->whereIn('id', $data['values']);
            });
        });
    }

    public function tableFilterGetOptionsByOwners(string $postableType): array
    {
        // statuses 1 - active
        return User::byStatuses(statuses: [1,])
            ->whereHas('cmsPosts', function (Builder $query) use ($postableType): Builder {
                return $query->where('postable_type', $postableType);
            })
            ->pluck('name', 'id')
            ->toArray();
    }

    public function tableFilterGetQueryByOwners(Builder $query, array $data): Builder
    {
        if (!$data['values'] || empty($data['values'])) {
            return $query;
        }

        return $query->whereHas('cmsPost', function (Builder $query) use ($data): Builder {
            return $query->whereHas('owner', function (Builder $query) use ($data): Builder {
                return $query->whereIn('id', $data['values']);
            });
        });
    }

    public function tableFilterGetQueryByStatuses(Builder $query, array $data): Builder
    {
        if (!$data['values'] || empty($data['values'])) {
            return $query;
        }

        return $query->whereHas('cmsPost', function (Builder $query) use ($data): Builder {
            return $query->whereIn('status', $data['values']);
        });
    }
}
