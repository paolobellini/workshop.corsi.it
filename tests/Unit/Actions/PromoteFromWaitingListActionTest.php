<?php

declare(strict_types=1);

use App\Actions\PromoteFromWaitingListAction;
use App\Models\User;
use App\Models\WaitingList;
use App\Models\Workshop;

it('promotes the first user from the waiting list', function () {
    $workshop = Workshop::factory()->create(['capacity' => 2]);
    $workshop->registrations()->attach(User::factory()->create());

    $first = User::factory()->create();
    $second = User::factory()->create();

    WaitingList::factory()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $first->id,
        'created_at' => '2026-04-01 10:00:00',
    ]);

    WaitingList::factory()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $second->id,
        'created_at' => '2026-04-01 11:00:00',
    ]);

    resolve(PromoteFromWaitingListAction::class)->handle($workshop);

    $workshop->refresh();

    expect($workshop->registrations()->where('users.id', $first->id)->exists())->toBeTrue()
        ->and($workshop->waitingList)->toHaveCount(1)
        ->and($workshop->waitingList->first()->user_id)->toBe($second->id);
});

it('skips user with overlapping workshop and promotes the next', function () {
    $workshop = Workshop::factory()->create([
        'capacity' => 5,
        'starts_at' => '2026-04-01 10:00:00',
        'ends_at' => '2026-04-01 14:00:00',
    ]);

    $conflicting = User::factory()->create();
    $overlappingWorkshop = Workshop::factory()->create([
        'starts_at' => '2026-04-01 12:00:00',
        'ends_at' => '2026-04-01 16:00:00',
    ]);
    $conflicting->workshops()->attach($overlappingWorkshop);

    $eligible = User::factory()->create();

    WaitingList::factory()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $conflicting->id,
        'created_at' => '2026-04-01 08:00:00',
    ]);

    WaitingList::factory()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $eligible->id,
        'created_at' => '2026-04-01 09:00:00',
    ]);

    resolve(PromoteFromWaitingListAction::class)->handle($workshop);

    $workshop->refresh();

    expect($workshop->registrations()->where('users.id', $eligible->id)->exists())->toBeTrue()
        ->and($workshop->registrations()->where('users.id', $conflicting->id)->exists())->toBeFalse()
        ->and($workshop->waitingList)->toHaveCount(0);
});

it('does nothing when the workshop is full', function () {
    $workshop = Workshop::factory()->create(['capacity' => 1]);
    $workshop->registrations()->attach(User::factory()->create());

    WaitingList::factory()->create(['workshop_id' => $workshop->id]);

    resolve(PromoteFromWaitingListAction::class)->handle($workshop);

    expect($workshop->waitingList)->toHaveCount(1)
        ->and($workshop->registrations)->toHaveCount(1);
});

it('does nothing when the waiting list is empty', function () {
    $workshop = Workshop::factory()->create(['capacity' => 5]);

    resolve(PromoteFromWaitingListAction::class)->handle($workshop);

    expect($workshop->registrations)->toHaveCount(0);
});
