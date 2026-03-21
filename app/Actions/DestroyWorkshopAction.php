<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workshop;
use Illuminate\Support\Facades\DB;

final class DestroyWorkshopAction
{
    public function __construct(private UnregisterUserAction $unregisterUserAction) {}

    public function handle(Workshop $workshop): void
    {
        DB::transaction(function () use ($workshop) {
            $this->unregisterUserAction->handle($workshop);
            $workshop->delete();
        });
    }
}
