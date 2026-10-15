<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
   
    public function create(): View
    {
        return view('auth.register');
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'min:6'],

            'username'=>['required', 'string', 'min:6', 'unique:' . User::class],

            'email' => ['required', 'string', 'lowercase', 'email', 'ends_with:@gmail.com', 'max:50', 'unique:' . User::class],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],

        ], [

            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.min' => 'Nama minimal 6 huruf.',
            'name.max' => 'Nama maksimal 50 huruf.',

            'username.required' => 'username wajib diisi.',
            'username.string' => 'username harus berupa teks.',
            'username.min' => 'username minimal 6 huruf.',
            'username.unique' => 'username sudah digunakan.',

            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.lowercase' => 'Email harus huruf kecil.',
            'email.email' => 'Format email tidak valid.',
            'email.ends_with' => 'Email harus menggunakan @gmail.com.',
            'email.max' => 'Email maksimal 50 huruf.',
            'email.unique' => 'Email sudah digunakan.',

            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username'=> $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('Peserta');

        event(new Registered($user));

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }
}