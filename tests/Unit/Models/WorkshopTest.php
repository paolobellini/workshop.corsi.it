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
