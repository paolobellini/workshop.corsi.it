<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\WorkshopFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable $ends_at
 * @property int $capacity
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read int $available_seats
 * @property-read bool $is_full
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $registrations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, WaitingList> $waitingList
 */
final class Workshop extends Model
{
    /** @use HasFactory<WorkshopFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $appends = ['available_seats', 'is_full'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'title' => 'string',
            'description' => 'string',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'capacity' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function registrations(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * @return HasMany<WaitingList, $this>
     */
    public function waitingList(): HasMany
    {
        return $this->hasMany(WaitingList::class)->oldest(); // @phpstan-ignore return.type
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function search(Builder $query, string $search): void
    {
        $query->where('title', 'like', "%{$search}%");
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function startingFrom(Builder $query, string $date): void
    {
        $query->whereDate('starts_at', '>=', $date);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function endingBefore(Builder $query, string $date): void
    {
        $query->where('ends_at', '<=', $date.' 23:59:59');
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function today(Builder $query): void
    {
        $query->whereDate('starts_at', today());
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function overlapping(Builder $query, self $workshop): void
    {
        $query->where('starts_at', '<', $workshop->ends_at)
            ->where('ends_at', '>', $workshop->starts_at);
    }

    protected function availableSeats(): Attribute
    {
        return Attribute::get(fn (): int => $this->capacity - $this->registrations()->count());
    }

    protected function isFull(): Attribute
    {
        return Attribute::get(fn (): bool => $this->available_seats <= 0);
    }
}
