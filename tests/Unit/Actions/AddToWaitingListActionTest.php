<?php

declare(strict_types=1);

use App\Actions\AddToWaitingListAction;
use App\Models\User;
use App\Models\WaitingList;
use App\Models\Workshop;
use Illuminate\Validation\ValidationException;

it('adds a user to the waiting list', function () {
    $workshop = Workshop::factory()->create(['capacity' => 1]);
    $workshop->registrations()->attach(User::factory()->create());
    $user = User::factory()->create();

    resolve(AddToWaitingListAction::class)->handle($workshop, $user);

    expect($workshop->waitingList)->toHaveCount(1)
        ->and($workshop->waitingList->first()->user_id)->toBe($user->id);
});

it('prevents adding a user with an overlapping workshop', function () {
    $user = User::factory()->create();

    $existing = Workshop::factory()->create([
        'starts_at' => '2026-04-01 10:00:00',
        'ends_at' => '2026-04-01 14:00:00',
    ]);
    $user->workshops()->attach($existing);

    $overlapping = Workshop::factory()->create([
        'starts_at' => '2026-04-01 13:00:00',
        'ends_at' => '2026-04-01 17:00:00',
        'capacity' => 0,
    ]);

    try {
        resolve(AddToWaitingListAction::class)->handle($overlapping, $user);
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('workshop');
        expect(WaitingList::query()->count())->toBe(0);

        return;
    }

    $this->fail('Expected ValidationException was not thrown.');
});
