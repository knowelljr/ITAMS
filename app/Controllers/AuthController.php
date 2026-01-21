<?php

namespace App\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    // Method to display the registration form
    public function register()
    {
        return view('auth.register');
    }

    // Method to handle registration
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    // Method to display the login form
    public function login()
    {
        return view('auth.login');
    }

    // Method to authenticate a user
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Method to logout a user
    public function logout()
    {
        Auth::logout();

        return redirect()->route('home');
    }

    // Method for password validation
    public function validatePassword($password)
    {
        return strlen($password) >= 8;
    }
}