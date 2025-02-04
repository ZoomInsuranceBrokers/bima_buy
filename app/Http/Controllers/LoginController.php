<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Session;
use App\Mail\OtpMail;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {

            if (Auth::user()->first_login) {
                return redirect()->route('logout');
            }
            
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
            $user = Auth::user();


            if ($user->first_login) {
                $otp = rand(100000, 999999);
                ;
                Session::put('otp', $otp);
                Session::put('otp_generated_at', now());
                Mail::to($user->email)->send(new OtpMail($otp));
                return redirect()->route('otp.form');
            }

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

    public function showOtpForm()
    {
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otpGeneratedAt = Session::get('otp_generated_at');
        $otp = Session::get('otp');

        if ($otp && $request->otp == $otp) {
            if (now()->diffInMinutes($otpGeneratedAt) <= 10) {
                $user->password = bcrypt($request->password);
                $user->first_login = false;
                $user->save();

                // Clear OTP and session data
                Session::forget('otp');
                Session::forget('otp_generated_at');

                return redirect()->route('logout');
                // Redirect to appropriate dashboard
                // switch ($user->role_id) {
                //     case 1: // Admin
                //         return redirect()->route('admin.dashboard');
                //     case 2: // Users
                //         return redirect()->route('user.dashboard');
                //     case 3: // ZM
                //         return redirect()->route('zm.dashboard');
                //     case 4: // Retail
                //         return redirect()->route('retail.dashboard');
                //     default:
                //         return redirect()->route('user1.dashboard');
                // }
            } else {
                // OTP expired
                return back()->withErrors(['otp' => 'OTP has expired. Please try again.'])->onlyInput('otp');
            }
        } else {
            return back()->withErrors(['otp' => 'Invalid OTP.'])->onlyInput('otp');
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

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Generate password reset token
        $user = User::where('email', $request->email)->first();
        $token = Hash::make(Str::random(60));
        $expiresAt = Carbon::now()->addMinutes(10);

        // Save token and expiration time to a password_resets table
        \DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
            'expires_at' => $expiresAt,
        ]);

        // Send reset email
        Mail::to($user->email)->send(new ResetPasswordMail($token));

        return back()->with('status', 'We have emailed your password reset link!');
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', compact('token'));
    }

    public function reset(Request $request)
    {
        // Validate the new password and token
        $request->validate([
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        // dd($request->token);
        // Check token expiration
        $passwordReset = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->orderBy('created_at', 'desc') // Order by the most recent request
            ->first();

        // dd($passwordReset);
        // if (!$passwordReset || !Hash::check($request->token, $passwordReset->token) || Carbon::parse($passwordReset->created_at)->addMinutes(10)->isPast()) {
        //     return redirect()->route('password.request')->withErrors(['token' => 'This password reset link is invalid or expired.']);
        // }

        if (!$passwordReset || Carbon::parse($passwordReset->created_at)->addMinutes(10)->isPast()) {
            return redirect()->route('password.request')->withErrors(['token' => 'This password reset link is invalid or expired.']);
        }

        // Find user and update the password
        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token after use
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }



    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
