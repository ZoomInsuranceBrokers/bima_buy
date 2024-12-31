<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
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
        }
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



    public function fetchNotifications(Request $request)
    {
        $userId = auth()->id();


        $notificationsQuery = Notification::where('receiver_id', $userId)
            ->with([
                'sender' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'image_path');
                }
            ])
            ->select('id', 'message', 'is_read', 'sender_id', 'created_at')
            ->orderBy('created_at', 'desc');


        if ($request->ajax()) {
            $notifications = $notificationsQuery->take(4)->get();

            // Prepare the notifications for the JSON response
            $notifications = $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'time_ago' => $this->calculateTimeAgo($notification->created_at),
                    'image_url' => asset('storage/' . $notification->sender->image_path),
                    'sender_name' => $notification->sender->first_name . ' ' . $notification->sender->last_name,
                ];
            });

            // Return the JSON response with the latest 4 notifications
            return response()->json(['notifications' => $notifications]);
        }


        $notifications = $notificationsQuery->get();

        // return $notifications;

        // Return the full notifications view
        return view('auth.notifications', compact('notifications'));
    }


    private function calculateTimeAgo($time)
    {
        $now = Carbon::now();
        $difference = $time->diff($now);

        if ($difference->y > 0)
            return $difference->y . ' year' . ($difference->y > 1 ? 's' : '');
        if ($difference->m > 0)
            return $difference->m . ' month' . ($difference->m > 1 ? 's' : '');
        if ($difference->d > 0)
            return $difference->d . ' day' . ($difference->d > 1 ? 's' : '');
        if ($difference->h > 0)
            return $difference->h . ' hour' . ($difference->h > 1 ? 's' : '');
        if ($difference->i > 0)
            return $difference->i . ' minute' . ($difference->i > 1 ? 's' : '');
        return $difference->s . ' second' . ($difference->s > 1 ? 's' : '');
    }

    public function markAsRead(Request $request)
    {
        $notification = Notification::find($request->id);

        if ($notification && $notification->receiver_id == auth()->id()) {
            $notification->is_read = 1;
            $notification->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found or unauthorized'], 403);
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
