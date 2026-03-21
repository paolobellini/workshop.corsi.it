<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\StoreWorkshopAction;
use App\Actions\UpdateWorkshopAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkshopRequest;
use App\Http\Requests\Admin\UpdateWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;

final class WorkshopController extends Controller
{
    public function store(StoreWorkshopRequest $request, StoreWorkshopAction $action): RedirectResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $action->handle($validated);

        return back()->with('success', 'Workshop creato con successo.');
    }

    public function update(UpdateWorkshopRequest $request, Workshop $workshop, UpdateWorkshopAction $action): RedirectResponse
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validated();
        $action->handle($workshop, $validated);

        return back()->with('success', 'Workshop aggiornato con successo.');
    }
}
