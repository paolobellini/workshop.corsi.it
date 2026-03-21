<?php

declare(strict_types=1);

use App\Actions\RegisterUserAction;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Validation\ValidationException;

it('registers a user to a workshop', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create();

    resolve(RegisterUserAction::class)->handle($workshop, $user);

    expect($workshop->registrations)->toHaveCount(1)
        ->and($workshop->registrations->first()->id)->toBe($user->id);
});

it('prevents registration when user has an overlapping workshop', function () {
    $user = User::factory()->create();

    $existing = Workshop::factory()->create([
        'starts_at' => '2026-04-01 10:00:00',
        'ends_at' => '2026-04-01 14:00:00',
    ]);
    $user->workshops()->attach($existing);

    $overlapping = Workshop::factory()->create([
        'starts_at' => '2026-04-01 13:00:00',
        'ends_at' => '2026-04-01 17:00:00',
    ]);

    try {
        resolve(RegisterUserAction::class)->handle($overlapping, $user);
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('workshop');

        return;
    }

    $this->fail('Expected ValidationException was not thrown.');
});

it('allows registration when workshops are adjacent and do not overlap', function () {
    $user = User::factory()->create();

    $existing = Workshop::factory()->create([
        'starts_at' => '2026-04-01 10:00:00',
        'ends_at' => '2026-04-01 14:00:00',
    ]);
    $user->workshops()->attach($existing);

    $adjacent = Workshop::factory()->create([
        'starts_at' => '2026-04-01 14:00:00',
        'ends_at' => '2026-04-01 18:00:00',
    ]);

    resolve(RegisterUserAction::class)->handle($adjacent, $user);

    expect($user->workshops()->count())->toBe(2);
});
