<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workshop;

final class UpdateWorkshopAction
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Workshop $workshop, array $attributes): Workshop
    {
        $workshop->update($attributes);

        return $workshop;
    }
}
