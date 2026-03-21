<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workshop;

final class UnregisterUserAction
{
    public function handle(Workshop $workshop, ?User $user = null): void
    {
        $workshop->registrations()->detach($user);
    }
}
