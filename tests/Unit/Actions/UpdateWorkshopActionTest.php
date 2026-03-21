<?php

declare(strict_types=1);

use App\Actions\UpdateWorkshopAction;
use App\Models\Workshop;

it('updates a workshop', function () {
    $workshop = Workshop::factory()->create(['title' => 'Original Title', 'capacity' => 20]);

    $updated = resolve(UpdateWorkshopAction::class)->handle($workshop, [
        'title' => 'Updated Title',
        'capacity' => 50,
    ]);

    expect($updated->title)->toBe('Updated Title')
        ->and($updated->capacity)->toBe(50);
});
