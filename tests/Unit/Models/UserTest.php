<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workshop;

it('has the correct visible keys', function () {
    $user = User::factory()->create()->fresh();

    expect(array_keys($user->toArray()))->toBe([
        'id',
        'name',
        'email',
        'email_verified_at',
        'created_at',
        'updated_at',
        'two_factor_confirmed_at',
    ]);
});

it('has workshops', function () {
    $user = User::factory()->create();
    $workshops = Workshop::factory()->count(2)->create();

    $user->workshops()->attach($workshops);

    expect($user->workshops)->toHaveCount(2);
});
