<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Models\User;
use App\Models\WaitingList;
use App\Models\Workshop;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guest cannot leave the waiting list', function () {
    $workshop = Workshop::factory()->create(['capacity' => 0]);
    $user = User::factory()->create();

    WaitingList::factory()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $user->id,
    ]);

    $response = $this->delete(route('workshops.waiting-list.destroy', $workshop));

    $response->assertRedirect(route('login'));
    $this->assertDatabaseCount('waiting_lists', 1);
});

test('employee can leave the waiting list', function () {
    $user = User::factory()->create()->assignRole(Roles::Employee);
    $workshop = Workshop::factory()->create();

    WaitingList::factory()->create([
        'workshop_id' => $workshop->id,
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->delete(route('workshops.waiting-list.destroy', $workshop));

    $response->assertRedirect();
    $this->assertDatabaseMissing('waiting_lists', [
        'user_id' => $user->id,
        'workshop_id' => $workshop->id,
    ]);
});
