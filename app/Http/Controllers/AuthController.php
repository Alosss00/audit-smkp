<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LoginLog;
use App\Models\User;
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
            if ($user->role === 'admin' || $user->role === 'auditor_smkp') {
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
                // Log failed attempt due to deactivated account
                LoginLog::create([
                    'user_id'    => $user->id,
                    'username'   => $request->username,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'device'     => LoginLog::parseDevice($request->userAgent()),
                    'status'     => 'failed',
                    'keterangan' => 'Akun dinonaktifkan oleh Administrator',
                    'login_at'   => now(),
                ]);

                AuditLog::create([
                    'user_id'         => $user->id,
                    'modul'           => 'Autentikasi',
                    'tindakan'        => "Percobaan login gagal: Akun '{$user->username}' berstatus nonaktif",
                    'data_lama'       => null,
                    'data_baru'       => ['ip' => $request->ip(), 'user_agent' => substr($request->userAgent() ?? '', 0, 150)],
                    'waktu_perubahan' => now(),
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Akun Anda telah dinonaktifkan oleh Administrator. Silakan hubungi admin.',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();

            // Record successful login in LoginLog
            $loginLog = LoginLog::create([
                'user_id'    => $user->id,
                'username'   => $user->username,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device'     => LoginLog::parseDevice($request->userAgent()),
                'status'     => 'success',
                'keterangan' => 'Login berhasil ke sistem',
                'login_at'   => now(),
            ]);

            // Save login ID and last activity timestamp in session for idle tracking
            $request->session()->put('current_login_log_id', $loginLog->id);
            $request->session()->put('last_activity_time', time());

            // Record to AuditLog
            AuditLog::create([
                'user_id'         => $user->id,
                'modul'           => 'Autentikasi',
                'tindakan'        => "User '{$user->name}' ({$user->username}) berhasil login ke sistem",
                'data_lama'       => null,
                'data_baru'       => ['ip' => $request->ip(), 'user_agent' => substr($request->userAgent() ?? '', 0, 150)],
                'waktu_perubahan' => now(),
            ]);

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang kembali, Administrator!');
            } elseif ($user->role === 'auditor_smkp') {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang kembali, Auditor SMKP!');
            }

            return redirect()->intended(route('auditor.dashboard'))
                ->with('success', 'Selamat datang kembali, Auditor Perusahaan!');
        }

        // Authentication failed - record in LoginLog & AuditLog
        $targetUser = User::where('username', $request->username)->first();

        LoginLog::create([
            'user_id'    => $targetUser?->id,
            'username'   => $request->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device'     => LoginLog::parseDevice($request->userAgent()),
            'status'     => 'failed',
            'keterangan' => 'Username atau password salah',
            'login_at'   => now(),
        ]);

        AuditLog::create([
            'user_id'         => $targetUser?->id ?? 1, // Fallback to admin/system if unknown
            'modul'           => 'Autentikasi',
            'tindakan'        => "Percobaan login gagal untuk username '{$request->username}' dari IP " . $request->ip(),
            'data_lama'       => null,
            'data_baru'       => ['username' => $request->username, 'ip' => $request->ip(), 'user_agent' => substr($request->userAgent() ?? '', 0, 150)],
            'waktu_perubahan' => now(),
        ]);

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
            $user = Auth::user();
            $logId = $request->session()->get('current_login_log_id');

            if ($logId) {
                LoginLog::where('id', $logId)->update([
                    'logout_at'  => now(),
                    'keterangan' => 'Logout manual oleh pengguna',
                ]);
            } else {
                LoginLog::create([
                    'user_id'    => $user->id,
                    'username'   => $user->username,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'device'     => LoginLog::parseDevice($request->userAgent()),
                    'status'     => 'logout',
                    'keterangan' => 'Logout manual oleh pengguna',
                    'login_at'   => now(),
                    'logout_at'  => now(),
                ]);
            }

            AuditLog::create([
                'user_id'         => $user->id,
                'modul'           => 'Autentikasi',
                'tindakan'        => "User '{$user->name}' logout dari sistem",
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

    /**
     * Keep session alive ping from client interaction.
     */
    public function keepAlive(Request $request)
    {
        if (Auth::check()) {
            $request->session()->put('last_activity_time', time());
            return response()->json([
                'status'        => 'ok',
                'last_activity' => time(),
            ]);
        }

        return response()->json([
            'status'   => 'unauthenticated',
            'redirect' => route('login'),
        ], 401);
    }

    /**
     * Auto-logout triggered when user inactivity timer expires on client-side.
     */
    public function autoLogout(Request $request)
    {
        $timeoutMinutes = (int) config('session.idle_timeout', 15);

        if (Auth::check()) {
            $user = Auth::user();
            $logId = $request->session()->get('current_login_log_id');

            if ($logId) {
                LoginLog::where('id', $logId)->update([
                    'logout_at'  => now(),
                    'status'     => 'timeout',
                    'keterangan' => "Auto-logout karena inaktivitas selama {$timeoutMinutes} menit",
                ]);
            } else {
                LoginLog::create([
                    'user_id'    => $user->id,
                    'username'   => $user->username,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'device'     => LoginLog::parseDevice($request->userAgent()),
                    'status'     => 'timeout',
                    'keterangan' => "Auto-logout karena inaktivitas selama {$timeoutMinutes} menit",
                    'login_at'   => now()->subMinutes($timeoutMinutes),
                    'logout_at'  => now(),
                ]);
            }

            AuditLog::create([
                'user_id'         => $user->id,
                'modul'           => 'Autentikasi',
                'tindakan'        => "User '{$user->name}' ({$user->username}) otomatis logout karena tidak ada aktivitas selama {$timeoutMinutes} menit",
                'data_lama'       => null,
                'data_baru'       => [
                    'ip'           => $request->ip(),
                    'idle_minutes' => $timeoutMinutes,
                    'auto_logout'  => true,
                ],
                'waktu_perubahan' => now(),
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'session_timeout' => true,
                'message'         => "Sesi Anda telah berakhir secara otomatis karena tidak ada aktivitas selama {$timeoutMinutes} menit.",
                'redirect'        => route('login'),
            ]);
        }

        return redirect()->route('login')->with(
            'warning',
            "Sesi Anda telah berakhir secara otomatis karena tidak ada aktivitas selama {$timeoutMinutes} menit. Silakan login kembali."
        );
    }
}
