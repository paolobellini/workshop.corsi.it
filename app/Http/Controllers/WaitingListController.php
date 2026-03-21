<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AddToWaitingListAction;
use App\Actions\RemoveFromWaitingListAction;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

final class WaitingListController extends Controller
{
    public function store(Workshop $workshop, #[CurrentUser] User $user, AddToWaitingListAction $action): RedirectResponse
    {
        $action->handle($workshop, $user);

        return back()->with('success', 'Sei stato aggiunto alla lista d\'attesa.');
    }

    public function destroy(Workshop $workshop, #[CurrentUser] User $user, RemoveFromWaitingListAction $action): RedirectResponse
    {
        $action->handle($workshop, $user);

        return back()->with('success', 'Sei stato rimosso dalla lista d\'attesa.');
    }
}
