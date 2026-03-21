<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\WaitingList;
use App\Models\Workshop;

it('has the correct visible keys', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create();

    $entry = WaitingList::query()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $user->id,
    ])->fresh();

    expect(array_keys($entry->toArray()))->toBe([
        'id',
        'workshop_id',
        'user_id',
        'created_at',
        'updated_at',
    ]);
});

it('belongs to a workshop', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create();

    $entry = WaitingList::query()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $user->id,
    ]);

    expect($entry->workshop)->toBeInstanceOf(Workshop::class)
        ->and($entry->workshop->id)->toBe($workshop->id);
});

it('belongs to a user', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create();

    $entry = WaitingList::query()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $user->id,
    ]);

    expect($entry->user)->toBeInstanceOf(User::class)
        ->and($entry->user->id)->toBe($user->id);
});
