<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', new PasswordRule(6)]
        ]);

        $passwordHash = Hash::make($request->get('password'));

        $user = User::create([
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => $passwordHash
        ]);

        return response()->json(status: 201, data: [ 'user' => $user ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string'
        ]);

        $loginSuccessful = Auth::attempt($request->only('email', 'password'));

        if (!$loginSuccessful) {
            $error = ['errors' => [ 'root' => ['We were unable to sign you in with those details.'] ]];
            return response()->json(status: 422, data: $error);
        }

        $request->session()->regenerate();
        return response()->json(status: 201, data: Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(status: 204);
    }

    public function user(Request $request)
    {
        return $request->user();
    }
}
