<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LoginLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    /**
     * Display a listing of login logs with stats & filtering.
     */
    public function index(Request $request)
    {
        $query = LoginLog::with('user')->latest('login_at');

        // Filter by Search (Username, Name, IP, Keterangan)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('device', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by Specific User
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Period
        $period = $request->get('period', 'all');
        if ($period === 'today') {
            $query->whereDate('login_at', Carbon::today());
        } elseif ($period === 'yesterday') {
            $query->whereDate('login_at', Carbon::yesterday());
        } elseif ($period === 'this_week') {
            $query->whereBetween('login_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'this_month') {
            $query->whereMonth('login_at', Carbon::now()->month)
                  ->whereYear('login_at', Carbon::now()->year);
        } elseif ($period === 'custom') {
            if ($request->filled('start_date')) {
                $query->whereDate('login_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('login_at', '<=', $request->end_date);
            }
        }

        $logs = $query->paginate(20)->withQueryString();

        // Calculate Summary Statistics
        $today = Carbon::today();
        $totalToday = LoginLog::whereDate('login_at', $today)->count();
        $successToday = LoginLog::whereDate('login_at', $today)->where('status', 'success')->count();
        $failedToday = LoginLog::whereDate('login_at', $today)->where('status', 'failed')->count();
        $timeoutTotal = LoginLog::where('status', 'timeout')->count();

        // Active Sessions (Logged in, no logout recorded, within last 4 hours)
        $activeSessions = LoginLog::where('status', 'success')
            ->whereNull('logout_at')
            ->where('login_at', '>=', Carbon::now()->subHours(4))
            ->count();

        $users = User::orderBy('name')->get(['id', 'name', 'username', 'role']);
        $idleTimeoutMinutes = (int) config('session.idle_timeout', 15);

        return view('admin.login_logs.index', compact(
            'logs',
            'users',
            'totalToday',
            'successToday',
            'failedToday',
            'timeoutTotal',
            'activeSessions',
            'idleTimeoutMinutes'
        ));
    }

    /**
     * Purge old login records to optimize storage.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|string|in:30,60,90,180,all_failed',
        ]);

        $days = $request->days;
        $deletedCount = 0;

        if ($days === 'all_failed') {
            $deletedCount = LoginLog::where('status', 'failed')->delete();
            $actionDesc = "Menghapus semua ({$deletedCount}) riwayat percobaan login gagal";
        } else {
            $cutoffDate = Carbon::now()->subDays((int) $days);
            $deletedCount = LoginLog::where('created_at', '<', $cutoffDate)->delete();
            $actionDesc = "Membersihkan {$deletedCount} data riwayat login yang lebih lama dari {$days} hari";
        }

        // Record cleanup in general AuditLog
        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Keamanan Sistem',
            'tindakan'        => $actionDesc,
            'data_lama'       => null,
            'data_baru'       => ['deleted_count' => $deletedCount, 'filter' => $days],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.login-logs.index')
            ->with('success', "Berhasil membersihkan {$deletedCount} data riwayat log login.");
    }
}
