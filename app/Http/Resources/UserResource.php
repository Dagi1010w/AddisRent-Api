<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // This is the public representation of a user.
        // We do NOT include the email or other private details.
        return [
            'id' => $this->id,
            'name' => $this->name, // This will be the person's name or the company name
            // We can add the user's profile picture here later if we add one.
        ];
    }
}