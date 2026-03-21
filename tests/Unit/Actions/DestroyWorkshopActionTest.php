<?php

declare(strict_types=1);

use App\Actions\DestroyWorkshopAction;
use App\Models\User;
use App\Models\Workshop;

it('deletes a workshop and detaches all registered users', function () {
    $workshop = Workshop::factory()->create();
    $users = User::factory()->count(3)->create();
    $workshop->registrations()->attach($users);

    resolve(DestroyWorkshopAction::class)->handle($workshop);

    expect($workshop->exists())->toBeFalse();
});
