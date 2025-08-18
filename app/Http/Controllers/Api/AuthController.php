<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
   

    // Handle user registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'nullable|string|max:15',
            'location_region' => 'required|string|max:255',
            'location_city' => 'nullable|string|max:255',
            'location_subcity' => 'nullable|string|max:255',
            'location_specific_area' => 'nullable|string|max:255',
            'type' => 'required|in:person,company',
            'preference' => 'required|in:tenant,buyer,seller,lessor',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'location_region' => $request->location_region ?? 'Default Region',
            'phone_number' => $request->phone_number ?? null,
            'location_city' => $request->location_city ?? 'Default City',
            'location_subcity' => $request->location_subcity ?? 'Default Subcity',
            'location_specific_area' => $request->location_specific_area ?? 'Default Area',
            'type' => $request->type ?? 'person',
            'preference' => $request->preference ?? 'tenant',
        ]);

        if ($user->type === 'person') {
        $user->personProfile()->updateOrCreate([], [
            'gender' => $request['gender'],
        ]);
    } elseif ($user->type === 'company') {
        $user->companyProfile()->updateOrCreate([], [
            'google_map_link' => $request['google_map_link'] ?? null,
            'business_license_path' => $request['business_license_path'] ?? null,
        ]);
    }

        if ($request->preference === 'tenant' || $request->preference === 'buyer') {
        $user->assignRole('seeker');
    } elseif ($request->preference === 'lessor' || $request->preference === 'seller') {
        $user->assignRole('lister');
    }



        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
    'token' => $token,
    'user'  => $user,// Return the roles as a string
], 201);
    }

     // Handle user login
     public function login(Request $request)
     {
         $credentials = $request->only('email', 'password');
 
         if (!Auth::attempt($credentials)) {
           
         return response()->json(['error' => 'Unauthorized'], 401);
         }

         $user = $request->user();


        //  $user = Auth::user();
         $user->tokens()->delete();
         $token = $user->createToken('authToken')->plainTextToken;

         return response()->json(['token' => $token, 'user' => $user], 200);
     }

    // Handle user logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}

