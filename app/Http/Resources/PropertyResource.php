<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage; // To be used if you store files locally

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * This method defines the "data contract" for a property in your API.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'listingType' => $this->listing_type, // Converting to camelCase for the frontend
            'propertyType' => $this->property_type,
            'status' => $this->status,
            
            'price' => [
                'amount' => number_format($this->price / 100, 2), // Formatted string for display
                'rawAmount' => $this->price / 100, // Unformatted number for calculations/forms
                'currency' => $this->currency,
            ],

            // --- Core Property Details ---
            'area' => $this->area,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'isFurnished' => (bool) $this->is_furnished, // Casting to a true boolean
            'amenities' => $this->amenities, // Laravel automatically decodes this from JSON

            // --- Location Details ---
            // A nested object for clean location data.
            'location' => [
                'region' => $this->address_region,
                'city' => $this->address_city,
                'subcity' => $this->address_subcity,
                'specificArea' => $this->address_specific_area,
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ],

            // --- Meta & Related Data ---
            'isFeatured' => (bool) $this->is_featured,
            'postedAt' => $this->created_at->diffForHumans(), // e.g., "2 weeks ago"
            
            // Conditionally include loaded relationships for efficiency.
            // These keys will only appear in the JSON if you used ->with() in your controller.
            'lister' => new UserResource($this->whenLoaded('lister')),
            'media' => PropertyMediaResource::collection($this->whenLoaded('media')),
            
            // --- Action URLs ---
            // It's a professional practice to provide the API endpoints directly in the resource.
            // This decouples the frontend from having to build URLs.
            // 'urls' => [
            //     'show' => route('properties.show', $this->id), // Assuming you have API routes named this
            //     'favorite' => route('favorites.toggle', $this->id),
            // ]
        ];
    }
}