<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\WaitingList;
use App\Models\Workshop;
use App\Rules\NoWorkshopOverlap;
use Illuminate\Support\Facades\Validator;

final class AddToWaitingListAction
{
    public function handle(Workshop $workshop, User $user): void
    {
        Validator::make(
            ['workshop' => $workshop],
            ['workshop' => new NoWorkshopOverlap($user)],
        )->validate();

        WaitingList::query()->create([
            'workshop_id' => $workshop->id,
            'user_id' => $user->id,
        ]);
    }
}
