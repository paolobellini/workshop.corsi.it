<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\Cache;

final class UnregisterUserAction
{
    public function __construct(private PromoteFromWaitingListAction $promoteAction) {}

    public function handle(Workshop $workshop, ?User $user = null): void
    {
        $detached = $workshop->registrations()->detach($user);

        if ($detached > 0) {
            Cache::tags(['workshops', 'stats'])->flush();

            if ($user !== null) {
                $this->promoteAction->handle($workshop);
            }
        }
    }
}
