<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
{
    $user = $request->user();

    $data = $request->validated();

    if (!empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    } else {
        unset($data['password']); // avoid overwriting password with null
    }

    $user->fill($data);
    
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }
    
    $user->save();

    // Update related profile
    if ($user->type === 'person') {
        $user->personProfile()->updateOrCreate([], [
            'gender' => $data['gender'],
        ]);
    } elseif ($user->type === 'company') {
        $user->companyProfile()->updateOrCreate([], [
            'google_map_link' => $data['google_map_link'] ?? null,
            'business_license_path' => $data['business_license_path'] ?? null,
        ]);
    }

    return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
}


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
