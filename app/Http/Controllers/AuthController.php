<?php

namespace App\Http\Controllers;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (User::count() === 0) {
            (new DatabaseSeeder())->run();
        }

        if (Auth::check()) {
            return Auth::user()->role === 'dokter'
                ? redirect('/dokter')
                : redirect('/operator');
        }

        $doctorAccounts = User::with('doctor')
            ->where('role', 'dokter')
            ->orderBy('id', 'asc')
            ->get();

        $operatorAccounts = User::where('role', 'operator')
            ->orderBy('id', 'asc')
            ->get();

        return view('auth.login', compact('doctorAccounts', 'operatorAccounts'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $target = Auth::user()->role === 'dokter' ? '/dokter' : '/operator';
            return redirect()->intended($target)->with('success', 'Selamat datang, ' . Auth::user()->name . '.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah keluar dari sesi staf.');
    }
}
