<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RolesAndPermissionsSeeder::class]);

        $admin = User::factory()->create([
            'name' => 'Paolo Bellini',
            'email' => 'paolo@bellini.one',
        ]);

        $admin->assignRole(Roles::Admin);

        $employee = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'doe@example.com',
        ]);

        $employee->assignRole(Roles::Employee);
    }
}
