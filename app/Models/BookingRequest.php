<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRequest extends Model
{
    /** @use HasFactory<\Database\Factories\BookingRequestFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'booking_requests';
    
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
     * The User (Seeker) who created/sent this booking request.
     * A BookingRequest BELONGS TO a User.
     */
    public function seeker(): BelongsTo
    {
        // Links this model's 'seeker_id' column to the 'id' on the User model.
        return $this->belongsTo(User::class, 'seeker_id');
    }

    /**
     * The Property that this booking request is for.
     * A BookingRequest BELONGS TO a Property.
     */
    public function property(): BelongsTo
    {
        // Links this model's 'property_id' column to the 'id' on the Property model.
        return $this->belongsTo(Property::class, 'property_id');
    }
}