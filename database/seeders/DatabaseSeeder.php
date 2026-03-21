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
        $admin = User::factory()->create([
            'name' => 'Paolo Bellini',
            'email' => 'paolo@bellini.one',
        ]);

        $user->assignRole(Roles::Admin);

        $employee = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'doe@example.com',
        ]);

        $user->assignRole(Roles::Employee);
    }
}
