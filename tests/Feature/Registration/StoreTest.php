<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests cannot register for a workshop', function () {
    $workshop = Workshop::factory()->create(['capacity' => 10]);

    $response = $this->post(route('workshops.register', $workshop));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('user_workshop', 0);
});

test('authenticated users can register for a workshop', function () {
    $workshop = Workshop::factory()->create(['capacity' => 10]);
    $user = User::factory()->create()->assignRole(Roles::Employee);

    $response = $this->actingAs($user)->post(route('workshops.register', $workshop));

    $response->assertRedirect();
    $this->assertDatabaseCount('user_workshop', 1);
    $this->assertDatabaseHas('user_workshop', [
        'workshop_id' => $workshop->id,
        'user_id' => $user->id,
    ]);
});

test('admins cannot register for a workshop', function () {
    $workshop = Workshop::factory()->create(['capacity' => 10]);
    $user = User::factory()->create()->assignRole(Roles::Admin);

    $response = $this->actingAs($user)->post(route('workshops.register', $workshop));

    $response->assertForbidden();
    $this->assertDatabaseCount('user_workshop', 0);
});
