<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin','staff','head'])],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'office' => ['required_if:role,staff,head', 'nullable', 'string', 'max:255'],
        ]);

        // The legacy form uses a "username" input, but the users table
        // may only have a "name" column. Store the username into the
        // `name` column so the insert succeeds regardless of schema.
        $user = User::create([
            'name' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        if (in_array($validated['role'], ['staff','head'])) {
            DB::table('staff_details')->insert([
                'user_id' => $user->id,
                'office' => $validated['office'] ?? null,
            ]);
        }

        return redirect()->route('login')->with('success', 'Registration successful! You can now sign in.');
    }
}
