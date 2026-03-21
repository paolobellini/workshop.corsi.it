<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workshop;

final class RegisterUserAction
{
    public function handle(Workshop $workshop, User $user): void
    {
        $workshop->registrations()->attach($user);
    }
}
