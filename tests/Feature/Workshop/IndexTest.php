<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workshop;

test('guests cannot view workshops', function () {
    $response = $this->get(route('workshops.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view workshops', function () {
    $user = User::factory()->create();
    Workshop::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('workshops.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('workshops/Index', false)
        ->has('workshops.data', 3)
        ->has('filters')
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
