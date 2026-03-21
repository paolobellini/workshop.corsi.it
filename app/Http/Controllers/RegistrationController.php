<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;

final class RegistrationController extends Controller
{
    public function store(Workshop $workshop): RedirectResponse
    {
        return back()->with('success', 'Iscrizione al workshop avvenuta con successo.');
    }
}
