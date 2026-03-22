<?php

declare(strict_types=1);

use App\Actions\SendWorkshopReminderAction;
use App\Models\User;
use App\Models\Workshop;
use App\Notifications\WorkshopReminderNotification;
use Illuminate\Support\Facades\Notification;

it('sends a reminder to all participants and returns the count', function () {
    Notification::fake();

    $workshop = Workshop::factory()->create();
    $participants = User::factory()->count(3)->create();
    $workshop->registrations()->attach($participants);

    $count = resolve(SendWorkshopReminderAction::class)->handle($workshop);

    expect($count)->toBe(3);
    Notification::assertSentTo($participants, WorkshopReminderNotification::class);
});

it('returns zero when workshop has no participants', function () {
    Notification::fake();

    $workshop = Workshop::factory()->create();

    $count = resolve(SendWorkshopReminderAction::class)->handle($workshop);

    expect($count)->toBe(0);
    Notification::assertNothingSent();
});
