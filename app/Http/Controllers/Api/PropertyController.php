<?php
namespace App\Http\Controllers\Api; // Updated namespace

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Resources\PropertyResource; // We'll rely on our Resource class
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PropertyController extends Controller
{
    // Enables the use of `$this->authorize()`
    use AuthorizesRequests;

    /**
     * PUBLIC: Display a paginated and filtered list of properties.
     */
    public function index(Request $request)
    {
        // Your excellent search/filter logic from PropertySearchController
        // belongs here, simplified with when().
        $properties = Property::query()
            ->where('status', 'active')
            ->with(['lister', 'media']) // Eager loading
            ->when($request->keywords, function ($query, $keywords) {
                $query->where(fn($q) => $q->where('title', 'like', "%{$keywords}%")->orWhere('description', 'like', "%{$keywords}%"));
            })
            ->when($request->listing_type, fn($q, $v) => $q->where('listing_type', $v))
            // ... add your other `when()` filters here ...
            ->latest()
            ->paginate(12);

        // ALWAYS return data wrapped in an API Resource for consistency. 
        return PropertyResource::collection($properties);
    }

    /**
     * PROTECTED: Store a newly created property.
     * This belongs to the authenticated lister.
     */
    public function store(Request $request)
    {
        // This is a protected route, so auth()->user() is guaranteed.
        if (!$request->user()->hasRole('lister')) {
             abort(403, 'Only listers can create properties.');
        }

        $validatedData = $this->validateProperty($request); // Use a helper for validation
        
        // Let the model handle JSON encoding automatically via the $casts property
        $property = $request->user()->properties()->create($validatedData);

        return new PropertyResource($property->load('media'));
    }

    /**
     * PUBLIC: Display a single property.
     */
    public function show(Property $property)
    {
        if ($property->status !== 'active') {
            abort(404);
        }
        $property->load(['lister.companyProfile', 'lister.personProfile', 'media']);

        return new PropertyResource($property);
    }

    /**
     * PROTECTED: Update a property.
     */
    public function update(Request $request, Property $property)
    {
        // 1. Use a Policy for authorization. It is much cleaner.
        $this->authorize('update', $property);
        
        // 2. Validate using the same helper method.
        $validatedData = $this->validateProperty($request);

        // 3. Update the model. Eloquent's 'update' handles this perfectly.
        $property->update($validatedData);
        
        // 4. Return the updated resource.
        return new PropertyResource($property->fresh()->load('media'));
    }

    /**
     * PROTECTED: Delete a property.
     */
    public function destroy(Property $property)
    {
        // 1. Authorize the deletion via a Policy.
        $this->authorize('delete', $property);
        
        // Add file cleanup logic here (e.g., delete from S3)
        
        // 2. Delete the model.
        $property->delete();
        
        // 3. Return a "No Content" response, the standard for successful deletions.
        return response()->json(null, 204);
    }

    /**
     * PROTECTED: Get properties for the currently logged-in Lister.
     */
    public function myProperties(Request $request)
    {
         $properties = $request->user()->properties()
                              ->with('media')
                              ->latest()
                              ->paginate(10);
         
         return PropertyResource::collection($properties);
    }


    // Reusable validation helper method
    protected function validateProperty(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'listing_type' => 'required|in:sale,rent',
            'property_type' => 'required|in:apartment,house,office,land,villa,shop,condo,studio,building,warehouse,guesthouse,other',
            'price' => 'required|numeric|min:0', // Still receives dollars from frontend
            'currency' => 'required|in:ETB,USD,GBP',
            'area' => 'required|integer|min:1',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'is_furnished' => 'required|boolean',
            'amenities' => 'nullable|array',
            'address_region' => 'required|string',
            'address_city' => 'required|string',
            'address_subcity' => 'required|string',
            'address_specific_area' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);
    }
}