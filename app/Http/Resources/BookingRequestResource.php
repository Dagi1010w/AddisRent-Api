<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'status' => $this->status,
            // Conditionally load the relationships to prevent errors
            'property' => new PropertyResource($this->whenLoaded('property')),
            'seeker' => new UserResource($this->whenLoaded('seeker')),
            'submitted_at' => $this->created_at->diffForHumans(),
        ];
    }
}