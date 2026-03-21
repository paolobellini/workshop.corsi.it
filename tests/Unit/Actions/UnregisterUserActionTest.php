<?php

declare(strict_types=1);

use App\Actions\UnregisterUserAction;
use App\Models\User;
use App\Models\WaitingList;
use App\Models\Workshop;

it('detaches a single user from a workshop', function () {
    $workshop = Workshop::factory()->create(['capacity' => 2]);
    $users = User::factory()->count(2)->create();
    $workshop->registrations()->attach($users);

    resolve(UnregisterUserAction::class)->handle($workshop, $users->first());

    expect($workshop->registrations()->count())->toBe(1)
        ->and($workshop->registrations->first()->id)->toBe($users->last()->id);
});

it('detaches all users from a workshop when no user is provided', function () {
    $workshop = Workshop::factory()->create(['capacity' => 3]);
    $users = User::factory()->count(3)->create();
    $workshop->registrations()->attach($users);

    resolve(UnregisterUserAction::class)->handle($workshop);

    expect($workshop->registrations()->count())->toBe(0);
});

it('promotes the first user from the waiting list after unregistering', function () {
    $workshop = Workshop::factory()->create(['capacity' => 1]);
    $registered = User::factory()->create();
    $workshop->registrations()->attach($registered);

    $waitingUser = User::factory()->create();
    WaitingList::factory()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $waitingUser->id,
    ]);

    resolve(UnregisterUserAction::class)->handle($workshop, $registered);

    expect($workshop->registrations()->count())->toBe(1)
        ->and($workshop->registrations->first()->id)->toBe($waitingUser->id)
        ->and($workshop->waitingList()->count())->toBe(0);
});
