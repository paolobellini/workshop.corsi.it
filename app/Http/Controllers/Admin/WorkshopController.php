<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\StoreWorkshopAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkshopRequest;
use Illuminate\Http\RedirectResponse;

final class WorkshopController extends Controller
{
    public function store(StoreWorkshopRequest $request, StoreWorkshopAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        return back()->with('success', 'Workshop created successfully.');
    }
}
