<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'due_date'    => $this->due_date,
            'assigned_to' => $this->assignedUser?->only(['id', 'name', 'email']),
            'created_by'  => $this->creator?->only(['id', 'name', 'email']),
            'dependencies' => $this->dependencies->pluck('id'),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
