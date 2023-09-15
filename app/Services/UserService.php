<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;

class UserService
{
    public function __construct(protected User $user)
    {
        $this->user = $user;
    }

    public function anonymizeUniqueEmailWhenDeleted(User $user): void
    {
        $user->email = $user->email . '//deleted_' . md5(uniqid());
        $user->save();
    }

    public function forceScopeActiveStatus(): Builder
    {
        // statuses 1 - active
        return $this->user->byStatuses(statuses: [1,]);
    }

    public function tableSearchByPhone(Builder $query, string $search): Builder
    {
        // $query->whereRaw('JSON_SEARCH(phones, "one", ?) IS NOT NULL', [$search]);

        return $query;
    }

    public function tableSearchByStatus(Builder $query, string $search): Builder
    {
        $statuses = UserStatus::asSelectArray();

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
        $statuses = UserStatus::asSelectArray();

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

    /**
     * $action can be:
     * Filament\Tables\Actions\DeleteAction;
     * Filament\Actions\DeleteAction;
     */
    // public function preventUserDeleteWithRelations($action, User $user): void
    // {
    //     if ($user->count() > 0) {
    //         Notification::make()
    //             ->title('Este usuário possui ... relacionados.')
    //             ->warning()
    //             ->body('Para excluir, você deve primeiro desvincular todos os ... deste usuário.')
    //             ->send();

    //         // $action->cancel();
    //         $action->halt();
    //     }
    // }
}
