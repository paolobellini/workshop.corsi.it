<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Workshop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class WorkshopReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Workshop $workshop) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Promemoria: {$this->workshop->title}")
            ->line("Ti ricordiamo che oggi si terrà il workshop \"{$this->workshop->title}\".")
            ->line("Inizio: {$this->workshop->starts_at->format('H:i')}")
            ->line("Fine: {$this->workshop->ends_at->format('H:i')}")
            ->line('Ti aspettiamo!');
    }
}
