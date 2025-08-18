<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
       'name',
        'email',
        'password',
        'phone_number',
        'location_region',
        'location_city',
        'location_subcity',
        'location_specific_area',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function personProfile()
    {
        return $this->hasOne(PersonProfile::class);
    }

    /**
     * Relation to company profile (one to one).
     */
    public function companyProfile()
    {
        return $this->hasOne(CompanyProfile::class);
    }

    /**
     * The properties that the user has favorited.
     */

     public function favorites(): BelongsToMany
     {
         return $this->belongsToMany(Property::class, 'favorites')
                    ->withTimestamps(); // <-- ADD THIS
     }

     /**
      * Properties that the user is related to
      */
      public function properties(): BelongsToMany
      {
          return $this->belongsToMany(Property::class);
      }

}
