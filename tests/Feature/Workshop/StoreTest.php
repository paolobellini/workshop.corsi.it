<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

function workshopData(array $overrides = []): array
{
    return array_merge([
        'title' => 'Laravel Testing Workshop',
        'description' => 'A comprehensive workshop on testing Laravel applications.',
        'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addWeek()->addHours(4)->format('Y-m-d H:i:s'),
        'capacity' => 30,
    ], $overrides);
}

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests cannot create a workshop', function () {
    $response = $this->post(route('workshops.store'), workshopData());

    $response->assertRedirect(route('login'));
    expect(Workshop::query()->count())->toBe(0);
});

test('employees cannot create a workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);

    $response = $this->actingAs($user)->post(route('workshops.store'), workshopData());

    $response->assertForbidden();
    expect(Workshop::query()->count())->toBe(0);
});

test('admins can create a workshop', function () {
    $user = User::factory()->create()->assignRole(Roles::Admin);

    $response = $this->actingAs($user)->post(route('workshops.store'), workshopData());

    $response->assertRedirect();
    $this->assertDatabaseCount(Workshop::class, 1);
    $this->assertDatabaseHas(Workshop::class, [
        'title' => 'Laravel Testing Workshop',
        'capacity' => 30,
    ]);
});

test('validation fails with invalid data', function () {
    $user = User::factory()->create()->assignRole(Roles::Admin);

    $response = $this->actingAs($user)->post(route('workshops.store'), workshopData([
        'title' => '',
        'capacity' => 0,
    ]));

    $response->assertRedirect();
    $response->assertSessionHasErrors(['title', 'capacity']);
    $this->assertDatabaseCount(Workshop::class, 0);
});
