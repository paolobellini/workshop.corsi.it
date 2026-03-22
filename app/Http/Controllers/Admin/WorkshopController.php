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
use App\Models\User;
use App\Models\Workshop;
use Carbon\CarbonImmutable;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class WorkshopController extends Controller
{
    public function index(IndexWorkshopRequest $request, #[CurrentUser] User $user): Response
    {
        $workshops = Workshop::query()
            ->when($request->validated('search'), fn ($query, $search) => $query->search($search))
            ->when($request->validated('start_date'), fn ($query, $date) => $query->startingFrom($date))
            ->when($request->validated('end_date'), fn ($query, $date) => $query->endingBefore($date))
            ->withCount('registrations')
            ->latest('starts_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('workshops/Index', [
            'workshops' => WorkshopResource::collection($workshops),
            'filters' => $request->only(['search', 'start_date', 'end_date']),
            ...($user->isAdmin() ? ['stats' => $this->stats()] : []),
        ]);
    }

    public function show(Workshop $workshop): Response
    {
        return Inertia::render('workshops/Show', [
            'workshop' => WorkshopResource::make($workshop->load('registrations')->loadCount('registrations')),
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

    /**
     * @return array{total: int, completed: int, upcoming: int, total_registrations: int}
     */
    private function stats(): array
    {
        return Cache::tags(['workshops', 'stats'])->remember('workshop_stats', 60, function (): array {
            $now = CarbonImmutable::now();

            $stats = Workshop::query()
                ->selectRaw('count(*) as total')
                ->selectRaw('count(case when ends_at < ? then 1 end) as completed', [$now])
                ->selectRaw('count(case when ends_at >= ? then 1 end) as upcoming', [$now])
                ->firstOrFail();

            $totalRegistrations = DB::table('user_workshop')->count();

            return [
                'total' => $stats->total,
                'completed' => $stats->completed,
                'upcoming' => $stats->upcoming,
                'total_registrations' => $totalRegistrations,
            ];
        });
    }
}
