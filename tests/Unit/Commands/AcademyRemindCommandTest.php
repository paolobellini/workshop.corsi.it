<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workshop;
use App\Notifications\WorkshopReminderNotification;
use Illuminate\Support\Facades\Notification;

it('sends reminders to participants of today\'s workshops', function () {
    Notification::fake();

    $workshop = Workshop::factory()->create([
        'starts_at' => today()->setTime(10, 0),
        'ends_at' => today()->setTime(14, 0),
    ]);
    $participants = User::factory()->count(2)->create();
    $workshop->registrations()->attach($participants);

    $this->artisan('academy:remind')
        ->expectsOutput("Reminders sent for \"{$workshop->title}\" (2 participants).")
        ->assertSuccessful();

    Notification::assertSentTo($participants, WorkshopReminderNotification::class);
});

it('does not send reminders for workshops on other days', function () {
    Notification::fake();

    $workshop = Workshop::factory()->create([
        'starts_at' => today()->addDay()->setTime(10, 0),
        'ends_at' => today()->addDay()->setTime(14, 0),
    ]);
    $workshop->registrations()->attach(User::factory()->create());

    $this->artisan('academy:remind')
        ->expectsOutput('No workshops scheduled for today.')
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('outputs info when no workshops are scheduled', function () {
    Notification::fake();

    $this->artisan('academy:remind')
        ->expectsOutput('No workshops scheduled for today.')
        ->assertSuccessful();

    Notification::assertNothingSent();
});
