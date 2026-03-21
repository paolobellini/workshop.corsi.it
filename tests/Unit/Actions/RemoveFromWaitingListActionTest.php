<?php

declare(strict_types=1);

use App\Actions\RemoveFromWaitingListAction;
use App\Models\User;
use App\Models\WaitingList;
use App\Models\Workshop;

it('removes a user from the waiting list', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create();

    WaitingList::query()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $user->id,
    ]);

    resolve(RemoveFromWaitingListAction::class)->handle($workshop, $user);

    expect($workshop->waitingList)->toHaveCount(0);
});
