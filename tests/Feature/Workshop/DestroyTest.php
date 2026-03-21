<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests cannot delete a workshop', function () {
    $workshop = Workshop::factory()->create();

    $response = $this->delete(route('workshops.destroy', $workshop));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas(Workshop::class, ['id' => $workshop->id]);
});

test('employees cannot delete a workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);
    $workshop = Workshop::factory()->create();

    $response = $this->actingAs($user)->delete(route('workshops.destroy', $workshop));

    $response->assertForbidden();
    $this->assertDatabaseHas(Workshop::class, ['id' => $workshop->id]);
});

test('admins can delete a workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Admin);
    $workshop = Workshop::factory()->create();
    $registeredUsers = User::factory()->count(2)->create();
    $workshop->registrations()->attach($registeredUsers);

    $response = $this->actingAs($user)->delete(route('workshops.destroy', $workshop));

    $response->assertRedirect();
    $this->assertDatabaseMissing(Workshop::class, ['id' => $workshop->id]);
    $this->assertDatabaseEmpty('user_workshop');
});
