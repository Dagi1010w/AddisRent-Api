<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * This method defines the "data contract" for a contact message in your API.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            
            // --- Meta Data ---
            'isRead' => (bool) $this->is_read,
            'submittedAt' => $this->created_at->diffForHumans(), // e.g., "2 weeks ago"
            
            // Conditionally include loaded relationships for efficiency
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
