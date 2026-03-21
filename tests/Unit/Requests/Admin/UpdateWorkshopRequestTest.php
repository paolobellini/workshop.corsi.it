<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Http\Requests\Admin\UpdateWorkshopRequest;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Validator;

function validUpdateWorkshopData(array $overrides = []): array
{
    return array_merge([
        'title' => 'Updated Workshop Title',
        'description' => 'An updated workshop description.',
        'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addWeek()->addHours(4)->format('Y-m-d H:i:s'),
        'capacity' => 50,
    ], $overrides);
}

it('passes with valid data', function () {
    $validator = Validator::make(validUpdateWorkshopData(), (new UpdateWorkshopRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails when required fields are missing', function (string $field) {
    $validator = Validator::make(validUpdateWorkshopData([$field => null]), (new UpdateWorkshopRequest)->rules());

    expect($validator->fails())->toBeTrue();
})->with(['title', 'starts_at', 'ends_at', 'capacity']);

it('fails when ends_at is before starts_at', function () {
    $validator = Validator::make(validUpdateWorkshopData([
        'starts_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        'ends_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
    ]), (new UpdateWorkshopRequest)->rules());

    expect($validator->fails())->toBeTrue();
});

it('authorizes admin users', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Roles::Admin);

    $this->actingAs($admin);

    expect((new UpdateWorkshopRequest)->authorize())->toBeTrue();
});

it('denies non-admin users', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $employee = User::factory()->create();
    $employee->assignRole(Roles::Employee);

    $this->actingAs($employee);

    expect((new UpdateWorkshopRequest)->authorize())->toBeFalse();
});
