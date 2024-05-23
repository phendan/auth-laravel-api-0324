<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
}
