<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guest cannot join the waiting list', function () {
    $workshop = Workshop::factory()->create(['capacity' => 0]);

    $response = $this->post(route('workshops.waiting-list.store', $workshop));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('waiting_lists', 0);
});

test('employee can join the waiting list for a full workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);

    $workshop = Workshop::factory()->create(['capacity' => 1]);
    $workshop->registrations()->attach(User::factory()->create());

    $response = $this->actingAs($user)->post(route('workshops.waiting-list.store', $workshop));

    $response->assertRedirect();
    $this->assertDatabaseHas('waiting_lists', [
        'user_id' => $user->id,
        'workshop_id' => $workshop->id,
    ]);
});

test('employee cannot join the waiting list for an overlapping workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);

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

    $response = $this->actingAs($user)->post(route('workshops.waiting-list.store', $overlapping));

    $response->assertRedirect();
    $response->assertSessionHasErrors('workshop');
    $this->assertDatabaseMissing('waiting_lists', [
        'user_id' => $user->id,
        'workshop_id' => $overlapping->id,
    ]);
});
