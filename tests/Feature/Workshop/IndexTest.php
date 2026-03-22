<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests cannot view workshops', function () {
    $response = $this->get(route('workshops.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view workshops', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);
    Workshop::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('workshops.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('workshops/Index', false)
        ->has('workshops.data', 3)
        ->has('filters')
        ->missing('stats')
    );
});

test('admins can view workshop stats', function () {
    $user = User::factory()->create()->assignRole(Roles::Admin);

    $completed = Workshop::factory()->create([
        'starts_at' => '2025-01-01 09:00:00',
        'ends_at' => '2025-01-01 17:00:00',
    ]);
    Workshop::factory()->create([
        'starts_at' => '2025-02-01 09:00:00',
        'ends_at' => '2025-02-01 17:00:00',
    ]);
    Workshop::factory()->create([
        'starts_at' => '2027-06-01 09:00:00',
        'ends_at' => '2027-06-01 17:00:00',
    ]);

    $completed->registrations()->attach(User::factory()->count(2)->create());

    $response = $this->actingAs($user)->get(route('workshops.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('workshops/Index', false)
        ->where('stats.total', 3)
        ->where('stats.completed', 2)
        ->where('stats.upcoming', 1)
        ->where('stats.total_registrations', 2)
    );
});

test('employees cannot view workshop stats', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);
    Workshop::factory()->create();

    $response = $this->actingAs($user)->get(route('workshops.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('workshops/Index', false)
        ->missing('stats')
    );
});

test('it filters workshops by search', function () {
    $user = User::factory()->create();
    Workshop::factory()->create(['title' => 'Laravel Testing']);
    Workshop::factory()->create(['title' => 'Vue Components']);

    $response = $this->actingAs($user)->get(route('workshops.index', ['search' => 'Laravel']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('workshops/Index', false)
        ->has('workshops.data', 1)
        ->where('filters.search', 'Laravel')
    );
});

test('it filters workshops by start date', function () {
    $user = User::factory()->create();
    Workshop::factory()->create(['starts_at' => '2026-04-01 09:00:00']);
    Workshop::factory()->create(['starts_at' => '2026-05-01 09:00:00']);

    $response = $this->actingAs($user)->get(route('workshops.index', ['start_date' => '2026-04-15']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('workshops/Index', false)
        ->has('workshops.data', 1)
        ->where('filters.start_date', '2026-04-15')
    );
});

test('it filters workshops by end date', function () {
    $user = User::factory()->create();
    Workshop::factory()->create(['ends_at' => '2026-04-01 17:00:00']);
    Workshop::factory()->create(['ends_at' => '2026-05-01 17:00:00']);

    $response = $this->actingAs($user)->get(route('workshops.index', ['end_date' => '2026-04-15']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('workshops/Index', false)
        ->has('workshops.data', 1)
        ->where('filters.end_date', '2026-04-15')
    );
});
