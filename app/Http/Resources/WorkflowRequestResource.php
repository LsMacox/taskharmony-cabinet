<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowRequestResource extends JsonResource
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
            'status' => $this->status,
            'author' => $this->author,
            'workflow' => new WorkflowResource($this->workflow),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
