<?php

declare(strict_types=1);

use App\Http\Resources\WorkshopResource;
use App\Models\Workshop;
use Illuminate\Http\Request;

it('returns the correct structure', function () {
    $workshop = Workshop::factory()->create();
    $resource = (new WorkshopResource($workshop))->toArray(new Request);

    expect($resource)->toHaveKeys([
        'id',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'capacity',
        'available_seats',
        'is_full',
        'created_at',
        'updated_at',
    ]);
});
