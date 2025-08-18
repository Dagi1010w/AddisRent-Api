<?php

namespace App\Http\Controllers\Api; // Updated namespace

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Resources\PropertyResource; // We'll reuse this to format the results

class FavoriteController extends Controller
{
    /**
     * Display a paginated list of the authenticated user's favorited properties.
     * PROTECTED: User must be authenticated.
     */
    public function index(Request $request)
    {
        // 1. Get the currently authenticated user
        $user = $request->user();

        // 2. Access the 'favorites' relationship, eager load related data, and paginate
        $favoritedProperties = $user->favorites()
                                    ->with(['lister', 'media']) // Eager load for performance
                                    ->latest('favorites.created_at') // Order by when they were favorited
                                    ->paginate(10);
        
        // 3. Return the results using our consistent PropertyResource
        return PropertyResource::collection($favoritedProperties);
    }

    /**
     * Toggles a property in the authenticated user's favorites list.
     * PROTECTED: User must be authenticated.
     */
    public function toggle(Request $request, Property $property)
    {
        // 1. Get the authenticated user
        $user = $request->user();

        // 2. The toggle() method is perfect for this. It attaches if not present,
        //    and detaches if present. It returns an array showing what was attached/detached.
        $result = $user->favorites()->toggle($property->id);

        // 3. Determine the outcome and return a meaningful JSON response
        $status = count($result['attached']) > 0 ? 'favorited' : 'unfavorited';
        
        return response()->json([
            'status' => $status,
        ]);
    }
}