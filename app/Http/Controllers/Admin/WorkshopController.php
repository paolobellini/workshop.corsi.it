<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;

final class WorkshopController extends Controller
{
    public function store(StoreWorkshopRequest $request): RedirectResponse
    {
        Workshop::query()->create($request->validated());

        return back()->with('success', 'Workshop created successfully.');
    }
}
