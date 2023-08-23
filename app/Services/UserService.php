<?php

namespace App\Services;

use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;

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
