<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workshop;

it('has the correct visible keys', function () {
    $workshop = Workshop::factory()->create()->fresh();

    expect(array_keys($workshop->toArray()))->toBe([
        'id',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'capacity',
        'created_at',
        'updated_at',
        'available_seats',
        'is_full',
    ]);
});

it('has registrations', function () {
    $workshop = Workshop::factory()->create();
    $users = User::factory()->count(3)->create();

    $workshop->registrations()->attach($users);

    expect($workshop->registrations)->toHaveCount(3);
});

it('returns the available seats', function () {
    $workshop = Workshop::factory()->create(['capacity' => 20]);
    $users = User::factory()->count(5)->create();

    $workshop->registrations()->attach($users);

    expect($workshop->available_seats)->toBe(15);
});

it('returns true when workshop is full', function () {
    $workshop = Workshop::factory()->create(['capacity' => 2]);
    $users = User::factory()->count(2)->create();

    $workshop->registrations()->attach($users);

    expect($workshop->is_full)->toBeTrue();
});

it('returns false when workshop is not full', function () {
    $workshop = Workshop::factory()->create(['capacity' => 10]);
    $user = User::factory()->create();

    $workshop->registrations()->attach($user);

    expect($workshop->is_full)->toBeFalse();
});

it('filters workshops by search scope', function () {
    Workshop::factory()->create(['title' => 'Laravel Workshop']);
    Workshop::factory()->create(['title' => 'Vue Workshop']);
    Workshop::factory()->create(['title' => 'Docker Training']);

    $results = Workshop::query()->search('Workshop')->get();

    expect($results)->toHaveCount(2)
        ->and($results->pluck('title')->toArray())->each->toContain('Workshop');
});

it('filters workshops by starting from scope', function () {
    Workshop::factory()->create(['starts_at' => '2026-03-10 09:00:00']);
    Workshop::factory()->create(['starts_at' => '2026-03-20 09:00:00']);
    Workshop::factory()->create(['starts_at' => '2026-03-25 09:00:00']);

    $results = Workshop::query()->startingFrom('2026-03-20')->get();

    expect($results)->toHaveCount(2);
});

it('filters workshops by ending before scope', function () {
    Workshop::factory()->create(['ends_at' => '2026-03-10 17:00:00']);
    Workshop::factory()->create(['ends_at' => '2026-03-15 17:00:00']);
    Workshop::factory()->create(['ends_at' => '2026-03-25 17:00:00']);

    $results = Workshop::query()->endingBefore('2026-03-15')->get();

    expect($results)->toHaveCount(2);
});
