<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Workshop;
use Illuminate\Support\Facades\Cache;

final class WorkshopObserver
{
    public function created(Workshop $workshop): void
    {
        $this->flushStats();
    }

    public function updated(Workshop $workshop): void
    {
        $this->flushStats();
    }

    public function deleted(Workshop $workshop): void
    {
        $this->flushStats();
    }

    private function flushStats(): void
    {
        Cache::tags(['workshops', 'stats'])->flush();
    }
}
