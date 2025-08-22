<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * This method defines the "data contract" for a booking request in your API.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'status' => $this->status,
            
            // Conditionally load the relationships to prevent errors
            'property' => new PropertyResource($this->whenLoaded('property')),
            'seeker' => new UserResource($this->whenLoaded('seeker')),
            
            'submittedAt' => $this->created_at->diffForHumans(), // e.g., "2 weeks ago"
        ];
    }
}
