<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;

final class PromoteFromWaitingListAction
{
    public function __construct(private RemoveFromWaitingListAction $removeAction) {}

    public function handle(Workshop $workshop): void
    {
        $user = $this->nextInLine($workshop);

        if ($user === null) {
            return;
        }

        DB::transaction(function () use ($workshop, $user): void {
            $this->removeAction->handle($workshop, $user);
            $workshop->registrations()->attach($user);
        });
    }

    private function nextInLine(Workshop $workshop): ?User
    {
        if ($workshop->is_full) {
            return null;
        }

        return $workshop->waitingList()->first()?->user;
    }
}
