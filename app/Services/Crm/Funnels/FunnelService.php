<?php

namespace App\Services\Crm\Funnels;

use App\Enums\DefaultStatus;
use App\Models\Crm\Funnels\Funnel;
use App\Models\Crm\Funnels\FunnelStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FunnelService
{
    public function __construct(protected Funnel $funnel, protected FunnelStage $funnelStage)
    {
        $this->funnel = $funnel;
        $this->funnelStage = $funnelStage;
    }

    public function getActiveFunnelsByRoles(Builder $query, array $roles): Builder
    {
        return $query->byRoles(roles: $roles)
            ->byStatuses([1,]); // 1 - active
    }

    public function getFunnelStagesByFunnel(?int $funnelId): Collection
    {
        return $this->funnelStage->where('funnel_id', $funnelId)
            ->pluck('name', 'id');
    }

    public function tableSearchByStatus(Builder $query, string $search): Builder
    {
        $statuses = DefaultStatus::asSelectArray();

        $matchingStatuses = [];
        foreach ($statuses as $index => $status) {
            if (stripos($status, $search) !== false) {
                $matchingStatuses[] = $index;
            }
        }

        if ($matchingStatuses) {
            return $query->whereIn('status', $matchingStatuses);
        }

        return $query;
    }

    public function tableSortByStatus(Builder $query, string $direction): Builder
    {
        $statuses = DefaultStatus::asSelectArray();

        $caseParts = [];
        $bindings = [];

        foreach ($statuses as $key => $status) {
            $caseParts[] = "WHEN ? THEN ?";
            $bindings[] = $key;
            $bindings[] = $status;
        }

        $orderByCase = "CASE status " . implode(' ', $caseParts) . " END";

        return $query->orderByRaw("$orderByCase $direction", $bindings);
    }

    public function ignoreClosingStages(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->whereNotIn('business_probability', [100, 0])
                ->orWhereNull('business_probability');
        });
    }
}
