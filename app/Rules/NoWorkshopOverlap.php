<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use App\Models\Workshop;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final class NoWorkshopOverlap implements ValidationRule
{
    public function __construct(private readonly User $user) {}

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $overlapping = Workshop::query()
            ->whereRelation('registrations', 'users.id', $this->user->id)
            ->overlapping($value)
            ->first();

        if ($overlapping instanceof Workshop) {
            $fail("L'orario del workshop si sovrappone a \"{$overlapping->title}\".");
        }
    }
}
