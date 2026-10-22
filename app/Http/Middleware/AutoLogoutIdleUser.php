<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use App\Models\LoginLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogoutIdleUser
{
    /**
     * Handle an incoming request.
     * Checks if the authenticated user has exceeded the idle inactivity threshold.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $timeoutMinutes = (int) config('session.idle_timeout', 15);
            $timeoutSeconds = $timeoutMinutes * 60;

            if ($request->session()->has('last_activity_time')) {
                $lastActivity = (int) $request->session()->get('last_activity_time');
                $elapsedSeconds = time() - $lastActivity;

                if ($elapsedSeconds >= $timeoutSeconds) {
                    // Update or create LoginLog with timeout status
                    $currentLogId = $request->session()->get('current_login_log_id');
                    if ($currentLogId) {
                        LoginLog::where('id', $currentLogId)->update([
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
                            'login_at'   => now()->subSeconds($elapsedSeconds),
                            'logout_at'  => now(),
                        ]);
                    }

                    // Record to AuditLog
                    AuditLog::create([
                        'user_id'         => $user->id,
                        'modul'           => 'Autentikasi',
                        'tindakan'        => "User '{$user->name}' ({$user->username}) otomatis logout karena tidak ada aktivitas selama {$timeoutMinutes} menit",
                        'data_lama'       => null,
                        'data_baru'       => [
                            'ip'            => $request->ip(),
                            'idle_minutes'  => round($elapsedSeconds / 60),
                            'auto_logout'   => true,
                        ],
                        'waktu_perubahan' => now(),
                    ]);

                    // Force logout
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'session_timeout' => true,
                            'message'         => "Sesi Anda telah berakhir secara otomatis karena tidak ada aktivitas selama {$timeoutMinutes} menit.",
                            'redirect'        => route('login'),
                        ], 401);
                    }

                    return redirect()->route('login')->with(
                        'warning',
                        "Sesi Anda telah berakhir secara otomatis karena tidak ada aktivitas selama {$timeoutMinutes} menit. Silakan login kembali."
                    );
                }
            }

            // Update last activity timestamp on legitimate activity
            $request->session()->put('last_activity_time', time());
        }

        return $next($request);
    }
}
