<?php

namespace App\Services\Business;

use App\Enums\Business\PaymentMethod;
use App\Enums\DefaultStatus;
use App\Models\Business\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BusinessService
{
    public function __construct(protected Business $business)
    {
        $this->business = $business;
    }

    public function tableSearchByPaymentMethod(Builder $query, string $search): Builder
    {
        $methods = PaymentMethod::asSelectArray();

        $matchingMethod = [];
        foreach ($methods as $index => $status) {
            if (stripos($status, $search) !== false) {
                $matchingMethod[] = $index;
            }
        }

        if ($matchingMethod) {
            return $query->whereIn('payment_method', $matchingMethod);
        }

        return $query;
    }

    public function tableSortByPaymentMethod(Builder $query, string $direction): Builder
    {
        $methods = PaymentMethod::asSelectArray();

        $caseParts = [];
        $bindings = [];

        foreach ($methods as $key => $status) {
            $caseParts[] = "WHEN ? THEN ?";
            $bindings[] = $key;
            $bindings[] = $status;
        }

        $orderByCase = "CASE payment_method " . implode(' ', $caseParts) . " END";

        return $query->orderByRaw("$orderByCase $direction", $bindings);
    }

    public function tableFilterByBusinessAt(Builder $query, array $data): Builder
    {
        return $query
            ->when(
                $data['business_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('business_at', '>=', $date),
            )
            ->when(
                $data['business_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('business_at', '<=', $date),
            );
    }

    public function tableDefaultSort(Builder $query, string $businessAtDirection = 'desc', string $createdAtDirection = 'desc'): Builder
    {
        return $query->orderBy('business_at', $businessAtDirection)
            ->orderBy('created_at', $createdAtDirection);
    }

    public function tableFilterGetOptionsByOwners(): array
    {
        // statuses 1 - active
        return User::byStatuses(statuses: [1,])
            ->whereHas('business')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function tableFilterGetQueryByOwners(Builder $query, array $data): Builder
    {
        if (!$data['values'] || empty($data['values'])) {
            return $query;
        }

        return $query->whereHas('owner', function (Builder $query) use ($data): Builder {
            return $query->whereIn('id', $data['values']);
        });
    }

    public function tableSortByPrice(Builder $query, string $direction): Builder
    {
        return $query->orderBy('price', $direction);
    }
}
