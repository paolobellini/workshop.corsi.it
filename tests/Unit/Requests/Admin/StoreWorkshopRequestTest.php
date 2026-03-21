<?php

declare(strict_types=1);

use App\Enums\Roles;
use App\Http\Requests\Admin\StoreWorkshopRequest;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Validator;

function validWorkshopData(array $overrides = []): array
{
    return array_merge([
        'title' => 'Laravel Testing Workshop',
        'description' => 'A comprehensive workshop on testing Laravel applications.',
        'starts_at' => now()->addWeek()->toDateTimeString(),
        'ends_at' => now()->addWeek()->addHours(4)->toDateTimeString(),
        'capacity' => 30,
    ], $overrides);
}

it('passes with valid data', function () {
    $validator = Validator::make(validWorkshopData(), (new StoreWorkshopRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails when required fields are missing', function (string $field) {
    $validator = Validator::make(validWorkshopData([$field => null]), (new StoreWorkshopRequest)->rules());

    expect($validator->fails())->toBeTrue();
})->with(['title', 'starts_at', 'ends_at', 'capacity']);

it('fails when ends_at is before starts_at', function () {
    $validator = Validator::make(validWorkshopData([
        'starts_at' => now()->addWeek()->toDateTimeString(),
        'ends_at' => now()->addDays(3)->toDateTimeString(),
    ]), (new StoreWorkshopRequest)->rules());

    expect($validator->fails())->toBeTrue();
});

it('authorizes admin users', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole(Roles::Admin);

    $this->actingAs($admin);

    expect((new StoreWorkshopRequest)->authorize())->toBeTrue();
});

it('denies non-admin users', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $employee = User::factory()->create();
    $employee->assignRole(Roles::Employee);

    $this->actingAs($employee);

    expect((new StoreWorkshopRequest)->authorize())->toBeFalse();
});
