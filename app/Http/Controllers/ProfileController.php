<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Employee; // <--- ADD THIS LINE HERE
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request)
{
    // 1. Create the Employee Record
    $employee = Employee::create($request->all());

    // 2. Automatically create a User Login for this employee
    User::create([
        'name' => $employee->name,
        'email' => $employee->email,
        'password' => Hash::make('nexpos@123'), // Default password
        'role' => 'cashier' // IMPORTANT: Assigns limited access
    ]);

    return redirect()->back()->with('success', 'Cashier added successfully');
}

    /**
     * Update the user's profile information.
     */
public function update(Request $request)
{
    $user = $request->user();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'image' => 'nullable|image|max:2048', // Validate image
    ]);

    // 1. Handle Image Upload
    if ($request->hasFile('image')) {
        // Delete old image
        if ($user->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->image);
        }
        // Save new image
        $path = $request->file('image')->store('profile-photos', 'public');
        $user->image = $path;
    }

    // 2. Handle Password Update (Optional)
    if ($request->filled('password')) {
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->save();

    return back()->with('success', 'Profile updated!');
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
