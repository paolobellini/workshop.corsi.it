<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workshop;
use App\Notifications\WorkshopReminderNotification;

final class SendWorkshopReminderAction
{
    public function handle(Workshop $workshop): int
    {
        $workshop->registrations->each(
            fn ($user) => $user->notify(new WorkshopReminderNotification($workshop))
        );

        return $workshop->registrations->count();
    }
}
