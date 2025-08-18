<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class PropertySearchController extends Controller
{
    /**
     * Search properties with filters.
     */
    public function search(Request $request)
    {
        $request->validate([
            'listing_type' => 'nullable|in:sale,rent',
            'property_type' => 'nullable|in:apartment,house,office,land,villa,shop,condo,studio,building,warehouse,guesthouse,other',
            'status' => 'nullable|in:active,inactive,pending,booked',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|in:ETB,USD,GBP',
            'min_area' => 'nullable|integer|min:0',
            'max_area' => 'nullable|integer|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'is_furnished' => 'nullable|boolean',
            'address_region' => 'nullable|string',
            'address_city' => 'nullable|string',
            'address_subcity' => 'nullable|string',
            'address_specific_area' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'keywords' => 'nullable|string|max:255',
        ]);

        $query = Property::query();

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('min_area')) {
            $query->where('area', '>=', $request->min_area);
        }

        if ($request->filled('max_area')) {
            $query->where('area', '<=', $request->max_area);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        if (!is_null($request->is_furnished)) {
            $query->where('is_furnished', $request->is_furnished);
        }

        if ($request->filled('address_region')) {
            $query->where('address_region', 'like', '%' . $request->address_region . '%');
        }

        if ($request->filled('address_city')) {
            $query->where('address_city', 'like', '%' . $request->address_city . '%');
        }

        if ($request->filled('address_subcity')) {
            $query->where('address_subcity', 'like', '%' . $request->address_subcity . '%');
        }
        
        if ($request->filled('address_specific_area')) {
            $query->where('address_specific_area', 'like', '%' . $request->address_specific_area . '%');
        }

        if (!is_null($request->is_featured)) {
            $query->where('is_featured', $request->is_featured);
        }

        if ($request->filled('keywords')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->keywords . '%')
                  ->orWhere('description', 'like', '%' . $request->keywords . '%');
            });
        }

        // You can add pagination here if needed
        $properties = $query->paginate(10);

        return view('properties.search_results', compact('properties'));
    }
}
