<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

function updateWorkshopData(array $overrides = []): array
{
    return array_merge([
        'title' => 'Updated Title',
        'description' => 'Updated description for the workshop.',
        'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addWeek()->addHours(4)->format('Y-m-d H:i:s'),
        'capacity' => 50,
    ], $overrides);
}

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests cannot update a workshop', function () {
    $workshop = Workshop::factory()->create();

    $response = $this->put(route('workshops.update', $workshop), updateWorkshopData());

    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing(Workshop::class, [
        'id' => $workshop->id,
        'title' => 'Updated Title',
    ]);
});

test('employees cannot update a workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);
    $workshop = Workshop::factory()->create();

    $response = $this->actingAs($user)->put(route('workshops.update', $workshop), updateWorkshopData());

    $response->assertForbidden();
    $this->assertDatabaseMissing(Workshop::class, [
        'id' => $workshop->id,
        'title' => 'Updated Title',
    ]);
});

test('admins can update a workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Admin);
    $workshop = Workshop::factory()->create();

    $response = $this->actingAs($user)->put(route('workshops.update', $workshop), updateWorkshopData());

    $response->assertRedirect();
    $this->assertDatabaseHas(Workshop::class, [
        'id' => $workshop->id,
        'title' => 'Updated Title',
        'capacity' => 50,
    ]);
});

test('validation fails with invalid data', function () {
    $user = User::factory()->create()->assignRole(Roles::Admin);
    $workshop = Workshop::factory()->create(['title' => 'Original Title']);

    $response = $this->actingAs($user)->put(route('workshops.update', $workshop), updateWorkshopData([
        'title' => '',
        'capacity' => 0,
    ]));

    $response->assertRedirect();
    $response->assertSessionHasErrors(['title', 'capacity']);
    $this->assertDatabaseHas(Workshop::class, [
        'id' => $workshop->id,
        'title' => 'Original Title',
    ]);
});
