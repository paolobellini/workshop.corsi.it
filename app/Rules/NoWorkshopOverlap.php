<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use App\Models\Workshop;
use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

final class NoWorkshopOverlap implements ValidationRule
{
    public function __construct(private readonly User $user) {}

    /**
     * @param  Workshop  $value
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $overlapping = Workshop::query()
            ->where(fn (Builder $query) => $query
                ->whereRelation('registrations', 'users.id', $this->user->id)
                ->orWhereRelation('waitingList', 'user_id', $this->user->id)
            )
            ->whereNot('id', $value->id)
            ->overlapping($value)
            ->first();

        if ($overlapping instanceof Workshop) {
            $fail("L'orario del workshop si sovrappone a \"{$overlapping->title}\".");
        }
    }
}
