<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('user cannot register for a workshop that overlaps with an existing one', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);

    $existing = Workshop::factory()->create([
        'starts_at' => '2026-04-01 10:00:00',
        'ends_at' => '2026-04-01 14:00:00',
    ]);
    $user->workshops()->attach($existing);

    $overlapping = Workshop::factory()->create([
        'starts_at' => '2026-04-01 13:00:00',
        'ends_at' => '2026-04-01 17:00:00',
    ]);

    $response = $this->actingAs($user)->post(route('workshops.register', $overlapping));

    $response->assertRedirect();
    $response->assertSessionHasErrors('workshop');
    $this->assertDatabaseMissing('user_workshop', [
        'user_id' => $user->id,
        'workshop_id' => $overlapping->id,
    ]);
});

test('user can register for a workshop that does not overlap', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);

    $existing = Workshop::factory()->create([
        'starts_at' => '2026-04-01 10:00:00',
        'ends_at' => '2026-04-01 14:00:00',
    ]);
    $user->workshops()->attach($existing);

    $nonOverlapping = Workshop::factory()->create([
        'starts_at' => '2026-04-01 14:00:00',
        'ends_at' => '2026-04-01 18:00:00',
    ]);

    $response = $this->actingAs($user)->post(route('workshops.register', $nonOverlapping));

    $response->assertRedirect();
    $this->assertDatabaseHas('user_workshop', [
        'user_id' => $user->id,
        'workshop_id' => $nonOverlapping->id,
    ]);
});
