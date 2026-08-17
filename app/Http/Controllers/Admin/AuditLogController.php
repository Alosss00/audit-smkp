<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display listing of user activity logs & file change history.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest('waktu_perubahan');

        // Filter by Module
        if ($request->filled('modul')) {
            $query->where('modul', $request->modul);
        }

        // Filter by Specific User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter File Changes Specifically
        if ($request->has('file_only') && $request->file_only == 1) {
            $query->where(function ($q) {
                $q->where('tindakan', 'like', '%upload%')
                  ->orWhere('tindakan', 'like', '%bukti%')
                  ->orWhere('tindakan', 'like', '%lampiran%')
                  ->orWhere('tindakan', 'like', '%file%');
            });
        }

        // Search in tindakan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tindakan', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20);

        $users = User::orderBy('name')->get();
        $modules = AuditLog::select('modul')->distinct()->pluck('modul');

        return view('admin.logs.index', compact('logs', 'users', 'modules'));
    }
}
