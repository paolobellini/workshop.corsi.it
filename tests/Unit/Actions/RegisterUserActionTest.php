<?php

declare(strict_types=1);

use App\Actions\RegisterUserAction;
use App\Models\User;
use App\Models\Workshop;

it('registers a user to a workshop', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create();

    resolve(RegisterUserAction::class)->handle($workshop, $user);

    expect($workshop->registrations)->toHaveCount(1)
        ->and($workshop->registrations->first()->id)->toBe($user->id);
});
