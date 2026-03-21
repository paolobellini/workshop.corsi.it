<?php

declare(strict_types=1);

use App\Http\Requests\Admin\IndexWorkshopRequest;
use Illuminate\Support\Facades\Validator;

it('authorizes all users', function () {
    expect((new IndexWorkshopRequest)->authorize())->toBeTrue();
});

it('passes with no filters', function () {
    $validator = Validator::make([], (new IndexWorkshopRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('passes with valid filters', function () {
    $validator = Validator::make([
        'search' => 'Laravel',
        'start_date' => '2026-04-01',
        'end_date' => '2026-04-30',
    ], (new IndexWorkshopRequest)->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails when end_date is before start_date', function () {
    $validator = Validator::make([
        'start_date' => '2026-04-30',
        'end_date' => '2026-04-01',
    ], (new IndexWorkshopRequest)->rules());

    expect($validator->fails())->toBeTrue();
});
