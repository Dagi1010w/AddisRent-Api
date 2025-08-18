<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyMedia extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyMediaFactory> */
    use HasFactory;

    /**
     * Define the table associated with the model.
     * Laravel is smart enough to guess this as 'property_media',
     * but explicitly defining it is good practice for clarity.
     *
     * @var string
     */
    protected $table = 'property_media';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    // =================================================================
    // Relationships
    // =================================================================

    /**
     * The Property this media file belongs to.
     * A PropertyMedia BELONGS TO a Property.
     */
    public function property(): BelongsTo
    {
        // Links this model's 'property_id' column to the 'id' on the Property model.
        return $this->belongsTo(Property::class);
    }
    
    // =================================================================
    // Accessors (Helpers)
    // =================================================================

    /**
     * An Accessor to get the full public URL of the media file.
     * This allows you to write `$media->url` in your code.
     * It handles the logic for different media types and storage.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        // If the media type is a 'video', we assume the 'path' is already a full
        // embeddable URL (e.g., a YouTube or Vimeo link).
        if ($this->media_type === 'video') {
            return $this->path;
        }

        // For 'photo' types, we use Laravel's Storage facade to generate a public URL.
        // This relies on the 'public' disk being correctly configured in `config/filesystems.php`
        // and the `php artisan storage:link` command having been run.
        if ($this->path) {
            return asset('storage/' . $this->path);
            
        }

        // Return a default placeholder image if, for some reason, a photo record
        // has no path. This prevents broken images on the frontend.
        return 'https://via.placeholder.com/800x600.png?text=Image+Not+Found';
    }
}