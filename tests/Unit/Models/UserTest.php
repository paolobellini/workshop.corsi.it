<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

it('can be created using its factory', function () {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->exists)->toBeTrue();
});

it('uses the expected traits', function () {
    $traits = class_uses_recursive(User::class);

    expect($traits)
        ->toContain(HasFactory::class)
        ->toContain(HasRoles::class)
        ->toContain(Notifiable::class)
        ->toContain(TwoFactorAuthenticatable::class);
});

it('hides sensitive attributes when serialized', function () {
    $user = User::factory()->create();

    $serialized = $user->toArray();

    expect($serialized)
        ->not->toHaveKey('password')
        ->not->toHaveKey('two_factor_secret')
        ->not->toHaveKey('two_factor_recovery_codes')
        ->not->toHaveKey('remember_token');
});

it('casts attributes to the correct types', function () {
    $user = User::factory()->create();

    expect($user->id)->toBeInt()
        ->and($user->name)->toBeString()
        ->and($user->email)->toBeString()
        ->and($user->email_verified_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
});

it('casts email_verified_at to null when unverified', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
});

it('can be created with two factor enabled', function () {
    $user = User::factory()->withTwoFactor()->create()->fresh();

    expect($user->two_factor_secret)->not->toBeNull()
        ->and($user->two_factor_recovery_codes)->not->toBeNull()
        ->and($user->two_factor_confirmed_at)->toBeInstanceOf(Carbon\CarbonImmutable::class);
});

it('hashes the password', function () {
    $user = User::factory()->create(['password' => 'secret-password']);

    expect($user->password)->not->toBe('secret-password');
});
