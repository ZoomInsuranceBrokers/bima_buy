<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'mobile' => 'required|numeric|digits:10',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {

            // echo Auth::user()->role_id;
            // exit;
            switch (Auth::user()->role_id) {
                case 1: // Admin
                    return redirect()->route('admin.dashboard');
                case 2: // Users
                    return redirect()->route('user.dashboard');
                case 3: // ZM
                    return redirect()->route('zm.dashboard');
                case 4: // Retail
                    return redirect()->route('retail.dashboard');
                default:
                    return redirect()->route('user1.dashboard');
            }
        } else {
            // Check if the mobile number exists
            $user = User::where('mobile', $request->mobile)->first();

            if (!$user) {
                return back()->withErrors([
                    'mobile' => 'Mobile number not found.',
                ])->onlyInput('mobile');
            } else {
                return back()->withErrors([
                    'password' => 'Password incorrect.',
                ])->onlyInput('password');
            }
        }
    }

    public function profile()
    {
        return view('auth.profile');
    }

    public function update(Request $request)
    {
        // Validate input
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'mobile' => 'required|digits:10|unique:users,mobile,' . Auth::id(),
            'email' => 'nullable|email|unique:users,email,' . Auth::id(),  // Adding email validation
        ]);

        // Update authenticated user's details
        $user = Auth::user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->gender = $request->gender;
        $user->mobile = $request->mobile;
        $user->email = $request->email;

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->image_path && file_exists(public_path('storage/' . $user->image_path))) {
                // Check if the current image is not the default photo
                if (!str_contains($user->image_path, 'default_photos')) {
                    unlink(storage_path('app/public/' . $user->image_path));
                }
            }

            // Store new profile picture with unique name
            $imageName = time() . '.' . $request->file('profile_picture')->getClientOriginalExtension();
            $imagePath = $request->file('profile_picture')->storeAs('profile_photos', $imageName, 'public');
            $user->image_path = $imagePath;
        }

        $user->save();

        // Redirect with success message
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }





    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
