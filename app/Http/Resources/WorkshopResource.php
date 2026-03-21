<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Workshop */
final class WorkshopResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Workshop $workshop */
        $workshop = $this->resource;

        $data = [
            'id' => $workshop->id,
            'title' => $workshop->title,
            'description' => $workshop->description,
            'starts_at' => $workshop->starts_at,
            'ends_at' => $workshop->ends_at,
            'capacity' => $workshop->capacity,
            'available_seats' => $workshop->available_seats,
            'is_full' => $workshop->is_full,
            'created_at' => $workshop->created_at,
            'updated_at' => $workshop->updated_at,
        ];

        if ($workshop->relationLoaded('registrations')) {
            $data['registrations'] = UserResource::collection($workshop->registrations);
        }

        return $data;
    }
}
