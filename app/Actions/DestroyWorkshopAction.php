<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workshop;

final class DestroyWorkshopAction
{
    public function handle(Workshop $workshop): void
    {
        $workshop->delete();
    }
}
