<?php

declare(strict_types=1);

use App\Actions\UnregisterUserAction;
use App\Models\User;
use App\Models\Workshop;

it('detaches a single user from a workshop', function () {
    $workshop = Workshop::factory()->create();
    $users = User::factory()->count(2)->create();
    $workshop->registrations()->attach($users);

    resolve(UnregisterUserAction::class)->handle($workshop, $users->first());

    expect($workshop->registrations()->count())->toBe(1)
        ->and($workshop->registrations->first()->id)->toBe($users->last()->id);
});

it('detaches all users from a workshop when no user is provided', function () {
    $workshop = Workshop::factory()->create();
    $users = User::factory()->count(3)->create();
    $workshop->registrations()->attach($users);

    resolve(UnregisterUserAction::class)->handle($workshop);

    expect($workshop->registrations()->count())->toBe(0);
});
