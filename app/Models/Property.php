<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyMedia;
use App\Models\User;
use App\Models\BookingRequest;


class Property extends Model
{
    protected $table = 'properties';

    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     * This is crucial for handling JSON, booleans, and ensuring data consistency.
     *
     * @var array
     */
    protected $casts = [
        'is_furnished' => 'boolean',
        'is_featured' => 'boolean',
        'amenities' => 'array', // Automatically decodes/encodes the JSON column
        'price' => 'integer',   // Stored in cents, so it's an integer
        'area' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    // =================================================================
    // Relationships
    // =================================================================

    /**
     * The User (Lister) who owns this property.
     * A Property BELONGS TO a User.
     */
    public function lister(): BelongsTo
    {
        // Links this model's 'lister_id' column to the 'id' on the User model.
        return $this->belongsTo(User::class, 'lister_id');
    }
    
    /**
     * The photos and videos associated with this property.
     * A Property HAS MANY media files.
     */
    public function media(): HasMany
    {
        // This relationship is ordered by the 'sort_order' column by default.
        return $this->hasMany(PropertyMedia::class)->orderBy('sort_order');
    }

    /**
     * All the booking requests made for this property.
     * A Property HAS MANY BookingRequests.
     */
    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }

    /**
     * The users who have favorited this property.
     * A Property BELONGS TO MANY Users (through the 'favorites' pivot table).
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // =================================================================
    // Accessors & Mutators (Helpers)
    // =================================================================
    
    /**
     * An "Accessor" to easily get the path of the main photo.
     * This allows you to write `$property->main_photo_url` in your code.
     *
     * @return string
     */
    public function getMainPhotoUrlAttribute(): string
    {
        // Find the first media item that is a photo.
        $mainPhoto = $this->media()->where('media_type', 'photo')->first();

        if ($mainPhoto && $mainPhoto->path) {
            // Assumes a 'public' disk. If using 's3', this should be changed.
            return asset('storage/' . $mainPhoto->path);
        }

        // Return a default placeholder image if no photo exists.
        return 'https://via.placeholder.com/800x600.png?text=No+Image';
    }

}
