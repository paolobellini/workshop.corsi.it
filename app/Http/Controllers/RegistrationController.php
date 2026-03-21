<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\RegisterUserAction;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

final class RegistrationController extends Controller
{
    public function store(Workshop $workshop, #[CurrentUser] User $user, RegisterUserAction $action): RedirectResponse
    {
        $action->handle($workshop, $user);

        return back()->with('success', 'Iscrizione al workshop avvenuta con successo.');
    }
}
