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

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
