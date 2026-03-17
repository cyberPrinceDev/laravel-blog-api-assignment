<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
            'id'            => $this->id,
            'title'         => $this->title,
            'content'       => $this->content,
            // Custom attribute: first 100 characters
            'short_content' => Str::limit($this->content, 100),
            // Accessing the related User model's name
            'author_name'   => $this->user->name, 
            // Formatted date (e.g., 2026-03-17 12:00:00)
            'created_at'    => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
