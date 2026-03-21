<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

it('returns the correct structure', function () {
    $user = User::factory()->create();
    $resource = (new UserResource($user))->toArray(new Request);

    expect($resource)
        ->toHaveKeys(['id', 'name', 'email'])
        ->not->toHaveKey('password')
        ->not->toHaveKey('remember_token');
});
