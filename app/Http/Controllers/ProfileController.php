<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function viewSelf() {
        $user = Auth::user();
        return view('profile.view', ['user' => $user]);
    }

    public function view($id) {
        $user = User::find($id);
        // TODO check if it exists
        return view('profile.view', ['user' => $user]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // TODO edit page
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }


    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fist-name' => ['required', 'string', 'max:255'],
            'last-name' => ['required', 'string', 'max:255'],
        ]);
        $user = Auth::user();
        $user->update([
            'first_name' => $validated['first-name'],
            'last_name' => $validated['last-name'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
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
