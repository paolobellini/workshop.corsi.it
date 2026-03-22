<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\SendWorkshopReminderAction;
use App\Models\Workshop;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('academy:remind')]
#[Description('Send a reminder email to all participants of today\'s workshops')]
final class AcademyRemindCommand extends Command
{
    public function handle(SendWorkshopReminderAction $action): void
    {
        $workshops = Workshop::query()
            ->with('registrations')
            ->today()
            ->get();

        if ($workshops->isEmpty()) {
            $this->info('No workshops scheduled for today.');

            return;
        }

        foreach ($workshops as $workshop) {
            $count = $action->handle($workshop);
            $this->info("Reminders sent for \"{$workshop->title}\" ({$count} participants).");
        }
    }
}
