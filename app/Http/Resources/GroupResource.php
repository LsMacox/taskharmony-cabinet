<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_name' => $this->parent?->name,
            'parent_id' => $this->parent_id,
            'description' => $this->description,
            'is_department' => (bool) $this->is_department,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'permissions' => $this->whenLoaded('users', function () {
                return $this->users->pluck('pivot.permissions')->first();
            }),
        ];
    }
}
