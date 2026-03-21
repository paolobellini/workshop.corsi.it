<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workshop;
use App\Rules\NoWorkshopOverlap;
use Illuminate\Support\Facades\Validator;

final class RegisterUserAction
{
    public function handle(Workshop $workshop, User $user): void
    {
        Validator::make(
            ['workshop' => $workshop],
            ['workshop' => new NoWorkshopOverlap($user)],
        )->validate();

        $workshop->registrations()->attach($user);
    }
}
