<?php

namespace App\Services\Crm\Contacts;

use App\Enums\DefaultStatus;
use App\Models\Crm\Contacts\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactService
{
    public function __construct(protected Contact $contact)
    {
        $this->contact = $contact;
    }

    public function forceScopeActiveStatus(Builder $query): Builder
    {
        return $query->with('contactable')
            ->whereHas('contactable')
            ->byStatuses([1,]);
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
}
