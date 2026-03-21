<?php

declare(strict_types=1);

use App\Actions\StoreWorkshopAction;
use App\Models\Workshop;

it('creates a workshop', function () {
    $attributes = [
        'title' => 'Laravel Testing Workshop',
        'description' => 'A workshop on testing.',
        'starts_at' => now()->addWeek()->toDateTimeString(),
        'ends_at' => now()->addWeek()->addHours(4)->toDateTimeString(),
        'capacity' => 30,
    ];

    $workshop = resolve(StoreWorkshopAction::class)->handle($attributes);

    expect($workshop)->toBeInstanceOf(Workshop::class)
        ->and($workshop->exists)->toBeTrue()
        ->and($workshop->title)->toBe('Laravel Testing Workshop')
        ->and($workshop->capacity)->toBe(30);
});
