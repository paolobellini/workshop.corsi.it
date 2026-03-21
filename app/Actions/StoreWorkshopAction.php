<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workshop;

final class StoreWorkshopAction
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): Workshop
    {
        return Workshop::query()->create($attributes);
    }
}
