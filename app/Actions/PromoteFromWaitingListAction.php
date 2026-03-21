<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\WaitingList;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class PromoteFromWaitingListAction
{
    public function __construct(
        private RemoveFromWaitingListAction $removeFromWaitingListAction,
        private RegisterUserAction $registerUserAction,
    ) {}

    public function handle(Workshop $workshop): void
    {
        if ($workshop->is_full) {
            return;
        }

        foreach ($workshop->waitingList as $entry) {
            if ($this->tryPromote($workshop, $entry)) {
                return;
            }
        }
    }

    private function tryPromote(Workshop $workshop, WaitingList $entry): bool
    {
        try {
            DB::transaction(function () use ($workshop, $entry): void {
                $this->removeFromWaitingListAction->handle($workshop, $entry->user);
                $this->registerUserAction->handle($workshop, $entry->user);
            });
        } catch (ValidationException) { // @phpstan-ignore catch.neverThrown
            $this->removeFromWaitingListAction->handle($workshop, $entry->user);

            return false;
        }

        return true;
    }
}
