<?php

declare(strict_types=1);

use App\Enums\Roles;

it('has the expected cases', function () {
    expect(Roles::cases())->toHaveCount(2)
        ->and(Roles::Admin->value)->toBe('admin')
        ->and(Roles::Employee->value)->toBe('employee');
});

it('returns the correct labels', function (Roles $role, string $expectedLabel) {
    expect($role->labels())->toBe($expectedLabel);
})->with([
    'admin' => [Roles::Admin, 'Amministratore'],
    'employee' => [Roles::Employee, 'Developer'],
]);
