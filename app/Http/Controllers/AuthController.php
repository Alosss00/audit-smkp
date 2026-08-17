<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('auditor.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Akun Anda telah dinonaktifkan oleh Administrator. Silakan hubungi admin.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            \App\Models\AuditLog::create([
                'user_id'         => $user->id,
                'modul'           => 'Autentikasi',
                'tindakan'        => "User '{$user->name}' ({$user->username}) berhasil login ke sistem",
                'data_lama'       => null,
                'data_baru'       => ['ip' => $request->ip(), 'user_agent' => substr($request->userAgent(), 0, 150)],
                'waktu_perubahan' => now(),
            ]);

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang kembali, Administrator!');
            }

            return redirect()->intended(route('auditor.dashboard'))
                ->with('success', 'Selamat datang kembali, Auditor!');
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            \App\Models\AuditLog::create([
                'user_id'         => Auth::id(),
                'modul'           => 'Autentikasi',
                'tindakan'        => "User '" . Auth::user()->name . "' logout dari sistem",
                'data_lama'       => null,
                'data_baru'       => null,
                'waktu_perubahan' => now(),
            ]);
        }

        Auth::logout();

        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah keluar dari sistem.');
    }
}
