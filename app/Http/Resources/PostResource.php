<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
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
            'user' => $this->user->name,
            'title' => $this->title,
            'description' => $this->description,
            'image' =>  asset('storage/' . $this->image),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'Comments' => commentResource::collection($this->comments->whereNull('parent_id')),
        ];
    }
}
