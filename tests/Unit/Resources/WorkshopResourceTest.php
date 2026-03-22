<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Http\Resources\WorkshopResource;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\Request;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

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

    $resource = new WorkshopResource($workshop)->toArray(new Request);

    expect($resource)
        ->toHaveKey('registrations')
        ->and($resource['registrations'])->toHaveCount(2);
});

it('excludes registrations when relation is not loaded', function () {
    $workshop = Workshop::factory()->create();

    $resource = new WorkshopResource($workshop)->toArray(new Request);

    expect($resource)->not->toHaveKey('registrations');
});

it('includes is_registered and is_on_waiting_list for employee users', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);
    $workshop = Workshop::factory()->create();

    $request = Request::create('/');
    $request->setUserResolver(fn () => $user);

    $resource = new WorkshopResource($workshop)->toArray($request);

    expect($resource)
        ->toHaveKey('is_registered', false)
        ->toHaveKey('is_on_waiting_list', false);
});
