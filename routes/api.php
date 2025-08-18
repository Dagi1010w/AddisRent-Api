<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\BookingRequestController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ContactFormController;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);



// Properties CRUD routes
Route::middleware('auth:sanctum')->group(function () {
   
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{property}', [PropertyController::class, 'update']);
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);
});


// Public Property  routes
Route::get('/property-search', [PropertyController::class, 'index']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);

// Contact form route
Route::post('/contact', [ContactFormController::class, 'store']);


// Booking Requests CRUD routes
Route::middleware('auth:sanctum')->group(function () {
     // --- Booking Request Routes ---
    
    // For Seekers to create a request
    Route::post('/properties/{property}/book', [BookingRequestController::class, 'store']);

    // For Listers to see the requests they have received
    Route::get('/my-bookings/received', [BookingRequestController::class, 'indexForLister']);
    
    // For Seekers to see the requests they have sent
    Route::get('/my-bookings/sent', [BookingRequestController::class, 'indexForSeeker']);
    
    // For Listers to approve/reject a request
    Route::put('/booking-requests/{bookingRequest}', [BookingRequestController::class, 'update']);
    
    // For Seekers to cancel their pending request
    Route::delete('/booking-requests/{bookingRequest}', [BookingRequestController::class, 'destroy']);
;
    // Add more booking request routes as needed
});

// Favorites routes
Route::middleware('auth:sanctum')->group(function () {
    // GET /api/favorites - Fetches all of the user's favorited properties
    Route::get('/favorites', [FavoriteController::class, 'index']);

    // POST /api/properties/{property}/favorite - Adds/removes a property from favorites
    Route::post('/properties/{property}/favorite', [FavoriteController::class, 'toggle']);
    // Add more favorites routes as needed
});

// Messaging routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages', [ContactFormController::class, 'send']);
    // Add more messaging routes as needed
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->get('/user/roles', function (Request $request) {
    return response()->json([
        'user' => $request->user()->name,
        'roles' => $request->user()->getRoleNames() // returns an array of roles
    ]);
});

