<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workshop;

final class RemoveFromWaitingListAction
{
    public function handle(Workshop $workshop, User $user): void
    {
        $workshop->waitingList()->where('user_id', $user->id)->delete();
    }
}
