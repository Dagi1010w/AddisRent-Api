<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PropertyMediaResource extends JsonResource
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
            'mediaType' => $this->media_type, // 'photo' or 'video'
            
            // This is the most important part.
            // It converts the stored relative path (e.g., 'property_photos/my_image.jpg')
            // into a full, publicly accessible URL.
            'url' => $this->getUrl(),
            
            'sortOrder' => $this->sort_order,
        ];
    }
    
    /**
     * Helper method to determine the correct URL.
     * This handles both local storage and potential video links.
     *
     * @return string
     */
    protected function getUrl(): string
    {
        // If the media type is a video, we assume the 'path' is already a full URL
        // (e.g., a YouTube or Vimeo link).
        if ($this->media_type === 'video') {
            return $this->path;
        }

       
        if ($this->path) {
            return asset('storage/' . $this->path); 
        }
        
        // Return a default placeholder image if no path is set for a photo.
        return 'https://via.placeholder.com/800x600.png?text=No+Image';
    }
}