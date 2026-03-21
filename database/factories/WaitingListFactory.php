<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\WaitingList;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WaitingList>
 */
final class WaitingListFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory(),
            'user_id' => User::factory(),
        ];
    }
}
