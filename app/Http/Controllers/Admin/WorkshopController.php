<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\DestroyWorkshopAction;
use App\Actions\StoreWorkshopAction;
use App\Actions\UpdateWorkshopAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexWorkshopRequest;
use App\Http\Requests\Admin\StoreWorkshopRequest;
use App\Http\Requests\Admin\UpdateWorkshopRequest;
use App\Http\Resources\WorkshopResource;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class WorkshopController extends Controller
{
    public function index(IndexWorkshopRequest $request): Response
    {
        $workshops = Workshop::query()
            ->withCount('registrations')
            ->latest('starts_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/workshops/Index', [
            'workshops' => WorkshopResource::collection($workshops),
        ]);
    }

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

    public function destroy(Workshop $workshop, DestroyWorkshopAction $action): RedirectResponse
    {
        $action->handle($workshop);

        return back()->with('success', 'Workshop eliminato con successo.');
    }
}
