<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests cannot view a workshop', function () {
    $workshop = Workshop::factory()->create();

    $response = $this->get(route('workshops.show', $workshop));

    $response->assertRedirect(route('login'));
});

test('employees cannot view a workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);
    $workshop = Workshop::factory()->create();

    $response = $this->actingAs($user)->get(route('workshops.show', $workshop));

    $response->assertForbidden();
});

test('admins can view a workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Admin);
    $workshop = Workshop::factory()->create();
    $registeredUsers = User::factory()->count(2)->create();
    $workshop->registrations()->attach($registeredUsers);

    $response = $this->actingAs($user)->get(route('workshops.show', $workshop));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('workshops/Show', false)
        ->has('workshop.data.id')
        ->has('workshop.data.registrations', 2)
    );
});
