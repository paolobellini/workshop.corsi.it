<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests cannot unregister from a workshop', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create();
    $workshop->registrations()->attach($user);

    $response = $this->delete(route('workshops.unregister', $workshop));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('user_workshop', 1);
});

test('authenticated users can unregister from a workshop', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create()->assignRole(Roles::Employee);
    $workshop->registrations()->attach($user);

    $response = $this->actingAs($user)->delete(route('workshops.unregister', $workshop));

    $response->assertRedirect();
    $this->assertDatabaseCount('user_workshop', 0);
});

test('admins cannot unregister from a workshop', function () {
    $workshop = Workshop::factory()->create();
    $user = User::factory()->create()->assignRole(Roles::Admin);
    $workshop->registrations()->attach($user);

    $response = $this->actingAs($user)->delete(route('workshops.unregister', $workshop));

    $response->assertForbidden();
    $this->assertDatabaseCount('user_workshop', 1);
});
