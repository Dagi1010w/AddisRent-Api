<?php

namespace App\Providers;

use App\Models\BookingRequest;
use App\Models\Property;
use Illuminate\Support\ServiceProvider;
use App\Policies\BookingRequestPolicy;
use App\Policies\PropertyPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    protected $policies = [
        BookingRequest::class => BookingRequestPolicy::class,
        Property::class => PropertyPolicy::class
    ];
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
