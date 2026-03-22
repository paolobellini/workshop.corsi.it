<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workshop;
use App\Notifications\WorkshopReminderNotification;

it('sends via mail channel', function () {
    $workshop = Workshop::factory()->create();
    $notification = new WorkshopReminderNotification($workshop);

    expect($notification->via(new stdClass))->toBe(['mail']);
});

it('contains the workshop details in the mail', function () {
    $workshop = Workshop::factory()->create([
        'title' => 'Laravel Testing',
        'starts_at' => '2026-04-01 09:00:00',
        'ends_at' => '2026-04-01 13:00:00',
    ]);

    $notification = new WorkshopReminderNotification($workshop);
    $mail = $notification->toMail(User::factory()->create());

    expect($mail->subject)->toBe('Promemoria: Laravel Testing')
        ->and($mail->introLines)->toContain('Ti ricordiamo che oggi si terrà il workshop "Laravel Testing".')
        ->and($mail->introLines)->toContain('Inizio: 09:00')
        ->and($mail->introLines)->toContain('Fine: 13:00');
});
