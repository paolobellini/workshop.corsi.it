<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workshop>
 */
final class WorkshopFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(2, true),
            'starts_at' => $startsAt = fake()->dateTimeBetween('+1 week', '+3 months'),
            'ends_at' => fake()->dateTimeBetween($startsAt, (clone $startsAt)->modify('+8 hours')),
            'capacity' => fake()->numberBetween(10, 100),
        ];
    }
}
