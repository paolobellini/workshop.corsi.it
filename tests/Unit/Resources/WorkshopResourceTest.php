<?php

declare(strict_types=1);

use App\Http\Resources\WorkshopResource;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\Request;

it('returns the correct structure', function () {
    $workshop = Workshop::factory()->create();
    $resource = (new WorkshopResource($workshop))->toArray(new Request);

    expect($resource)->toHaveKeys([
        'id',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'capacity',
        'available_seats',
        'is_full',
        'created_at',
        'updated_at',
    ])->not->toHaveKey('registrations');
});

it('includes registrations when relation is loaded', function () {
    $workshop = Workshop::factory()->create();
    $users = User::factory()->count(2)->create();
    $workshop->registrations()->attach($users);
    $workshop->load('registrations');

    $resource = (new WorkshopResource($workshop))->toArray(new Request);

    expect($resource)
        ->toHaveKey('registrations')
        ->and($resource['registrations'])->toHaveCount(2);
});

it('excludes registrations when relation is not loaded', function () {
    $workshop = Workshop::factory()->create();

    $resource = (new WorkshopResource($workshop))->toArray(new Request);

    expect($resource)->not->toHaveKey('registrations');
});
