<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class UpdateWorkshopRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole(Roles::Admin) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['nullable', 'string', 'min:10', 'max:5000'],
            'starts_at' => ['required', 'date_format:Y-m-d H:i:s', 'after_or_equal:now'],
            'ends_at' => ['required', 'date_format:Y-m-d H:i:s', 'after:starts_at'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }
}
