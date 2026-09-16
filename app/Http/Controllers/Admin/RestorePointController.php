<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuditSesi;
use App\Models\Departemen;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\RestorePoint;
use App\Models\SubElemen;
use App\Models\User;
use App\Services\RestorePointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class RestorePointController extends Controller
{
    protected RestorePointService $restoreService;

    public function __construct(RestorePointService $restoreService)
    {
        $this->restoreService = $restoreService;
    }

    /**
     * Display listing of restore points & all soft-deleted records (Recycle Bin).
     */
    public function index(Request $request)
    {
        // 1. System Restore Points Snapshots Query
        $rpQuery = RestorePoint::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $rpQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipe')) {
            $rpQuery->where('tipe', $request->tipe);
        }

        $restorePoints = $rpQuery->paginate(15);
        $totalCount = RestorePoint::count();
        $latestPoint = RestorePoint::latest()->first();

        // 2. Aggregate All Soft-Deleted Data Across All Modules
        $deletedList = collect();

        // Sesi Audit
        AuditSesi::onlyTrashed()->with(['perusahaan', 'departemen'])->get()->each(function ($item) use ($deletedList) {
            $deletedList->push([
                'id'          => $item->id,
                'type'        => 'audit_sesi',
                'module'      => 'Sesi Audit',
                'badge_class' => 'bg-primary',
                'icon'        => 'bi-journal-check',
                'title'       => 'Sesi Audit #' . $item->id . ($item->perusahaan ? ' — ' . $item->perusahaan->nama_perusahaan : ''),
                'info'        => 'Periode: ' . ($item->periode ?? '-') . ' | Area: ' . ($item->departemen->nama_departemen ?? 'Semua Area'),
                'deleted_at'  => $item->deleted_at,
            ]);
        });

        // Users
        User::onlyTrashed()->get()->each(function ($item) use ($deletedList) {
            $deletedList->push([
                'id'          => $item->id,
                'type'        => 'user',
                'module'      => 'User Pengguna',
                'badge_class' => 'bg-warning text-dark',
                'icon'        => 'bi-person',
                'title'       => $item->name . ' (' . $item->username . ')',
                'info'        => 'Role: ' . ucfirst($item->role) . ' | Email: ' . ($item->email ?? '-'),
                'deleted_at'  => $item->deleted_at,
            ]);
        });

        // Perusahaan
        Perusahaan::onlyTrashed()->get()->each(function ($item) use ($deletedList) {
            $deletedList->push([
                'id'          => $item->id,
                'type'        => 'perusahaan',
                'module'      => 'Perusahaan',
                'badge_class' => 'bg-info text-dark',
                'icon'        => 'bi-building',
                'title'       => $item->nama_perusahaan,
                'info'        => 'Kode: ' . ($item->kode_perusahaan ?? '-') . ' | PIC: ' . ($item->penanggung_jawab ?? '-'),
                'deleted_at'  => $item->deleted_at,
            ]);
        });

        // Departemen
        Departemen::onlyTrashed()->get()->each(function ($item) use ($deletedList) {
            $deletedList->push([
                'id'          => $item->id,
                'type'        => 'departemen',
                'module'      => 'Departemen',
                'badge_class' => 'bg-success',
                'icon'        => 'bi-diagram-3-fill',
                'title'       => $item->nama_departemen,
                'info'        => 'Kode: ' . ($item->kode_departemen ?? '-'),
                'deleted_at'  => $item->deleted_at,
            ]);
        });

        // Elemen
        Elemen::onlyTrashed()->get()->each(function ($item) use ($deletedList) {
            $deletedList->push([
                'id'          => $item->id,
                'type'        => 'elemen',
                'module'      => 'Elemen SMKP',
                'badge_class' => 'bg-secondary',
                'icon'        => 'bi-folder',
                'title'       => 'Elemen ' . $item->nomor_elemen . ': ' . $item->nama_elemen,
                'info'        => 'Bobot: ' . ($item->bobot_persen ?? 0) . '%',
                'deleted_at'  => $item->deleted_at,
            ]);
        });

        // Sub Elemen
        SubElemen::onlyTrashed()->with('elemen')->get()->each(function ($item) use ($deletedList) {
            $deletedList->push([
                'id'          => $item->id,
                'type'        => 'sub_elemen',
                'module'      => 'Sub-Elemen',
                'badge_class' => 'bg-dark',
                'icon'        => 'bi-diagram-3',
                'title'       => 'Sub-Elemen ' . $item->nomor_sub_elemen . ': ' . $item->nama_sub_elemen,
                'info'        => 'Induk: Elemen ' . ($item->elemen->nomor_elemen ?? '-'),
                'deleted_at'  => $item->deleted_at,
            ]);
        });

        // Kriteria
        Kriteria::onlyTrashed()->with('subElemen')->get()->each(function ($item) use ($deletedList) {
            $deletedList->push([
                'id'          => $item->id,
                'type'        => 'kriteria',
                'module'      => 'Kriteria',
                'badge_class' => 'bg-danger',
                'icon'        => 'bi-list-check',
                'title'       => 'Kriteria ' . $item->nomor_kriteria . ': ' . substr($item->deskripsi_kriteria, 0, 80) . '...',
                'info'        => 'Sub-Elemen: ' . ($item->subElemen->nomor_sub_elemen ?? '-'),
                'deleted_at'  => $item->deleted_at,
            ]);
        });

        // Compute counts per module before filtering
        $deletedCountsByModule = [
            'all'         => $deletedList->count(),
            'audit_sesi'  => $deletedList->where('type', 'audit_sesi')->count(),
            'user'        => $deletedList->where('type', 'user')->count(),
            'perusahaan'  => $deletedList->where('type', 'perusahaan')->count(),
            'departemen'  => $deletedList->where('type', 'departemen')->count(),
            'elemen'      => $deletedList->where('type', 'elemen')->count(),
            'sub_elemen'  => $deletedList->where('type', 'sub_elemen')->count(),
            'kriteria'    => $deletedList->where('type', 'kriteria')->count(),
        ];

        // Filter Trash List
        if ($request->filled('trash_module')) {
            $deletedList = $deletedList->where('type', $request->trash_module);
        }

        if ($request->filled('trash_search')) {
            $searchWord = strtolower($request->trash_search);
            $deletedList = $deletedList->filter(function ($item) use ($searchWord) {
                return str_contains(strtolower($item['title']), $searchWord) ||
                       str_contains(strtolower($item['info']), $searchWord) ||
                       str_contains(strtolower($item['module']), $searchWord);
            });
        }

        // Sort deleted items by deleted_at descending
        $deletedItems = $deletedList->sortByDesc('deleted_at');
        $totalDeletedCount = $deletedCountsByModule['all'];

        return view('admin.restore_points.index', compact(
            'restorePoints',
            'totalCount',
            'latestPoint',
            'deletedItems',
            'totalDeletedCount',
            'deletedCountsByModule'
        ));
    }

    /**
     * Restore a specific soft-deleted item.
     */
    public function restoreItem(Request $request, $type, $id)
    {
        $model = $this->resolveModel($type, $id, true);
        if (!$model) {
            return back()->with('error', 'Data terhapus tidak ditemukan atau sudah dipulihkan.');
        }

        $title = $this->getModelTitle($type, $model);
        $model->restore();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Restore Point',
            'tindakan'        => "Memulihkan data terhapus ({$type}): {$title}",
            'data_lama'       => null,
            'data_baru'       => ['type' => $type, 'id' => $id, 'title' => $title],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.restore-points.index', ['tab' => 'trash'])
            ->with('success', "Data '{$title}' berhasil dipulihkan kembali ke sistem!");
    }

    /**
     * Permanently force delete a specific soft-deleted item.
     */
    public function forceDeleteItem(Request $request, $type, $id)
    {
        $model = $this->resolveModel($type, $id, true);
        if (!$model) {
            return back()->with('error', 'Data terhapus tidak ditemukan.');
        }

        $title = $this->getModelTitle($type, $model);
        $model->forceDelete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Restore Point',
            'tindakan'        => "Menghapus PERMANEN data ({$type}): {$title}",
            'data_lama'       => ['type' => $type, 'id' => $id, 'title' => $title],
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.restore-points.index', ['tab' => 'trash'])
            ->with('success', "Data '{$title}' berhasil dihapus permanen dari sistem.");
    }

    /**
     * Restore all soft-deleted items across all modules or a specific module.
     */
    public function restoreAll(Request $request)
    {
        $module = $request->input('module', 'all');
        $restoredCount = 0;

        $targetTypes = ($module === 'all') 
            ? ['audit_sesi', 'user', 'perusahaan', 'departemen', 'elemen', 'sub_elemen', 'kriteria']
            : [$module];

        foreach ($targetTypes as $type) {
            $class = $this->getModelClass($type);
            if ($class) {
                $trashed = $class::onlyTrashed()->get();
                $restoredCount += $trashed->count();
                foreach ($trashed as $item) {
                    $item->restore();
                }
            }
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Restore Point',
            'tindakan'        => "Memulihkan SEMUA data terhapus ({$restoredCount} entri)",
            'data_lama'       => null,
            'data_baru'       => ['restored_count' => $restoredCount, 'module' => $module],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.restore-points.index', ['tab' => 'trash'])
            ->with('success', "Sebanyak {$restoredCount} data terhapus berhasil dipulihkan!");
    }

    /**
     * Empty trash / permanently delete all soft-deleted items.
     */
    public function emptyTrash(Request $request)
    {
        $request->validate([
            'password_confirmation' => 'required|string',
        ], [
            'password_confirmation.required' => 'Kata sandi Admin wajib diisi untuk mengosongkan tempat sampah.',
        ]);

        if (!Hash::check($request->password_confirmation, auth()->user()->password)) {
            return back()->with('error', 'Konfirmasi Kata Sandi Admin tidak sesuai. Pengosongan data dibatalkan.');
        }

        $deletedCount = 0;
        $targetTypes = ['audit_sesi', 'user', 'perusahaan', 'departemen', 'elemen', 'sub_elemen', 'kriteria'];

        foreach ($targetTypes as $type) {
            $class = $this->getModelClass($type);
            if ($class) {
                $trashed = $class::onlyTrashed()->get();
                $deletedCount += $trashed->count();
                foreach ($trashed as $item) {
                    $item->forceDelete();
                }
            }
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Restore Point',
            'tindakan'        => "Mengosongkan tempat sampah (Hapus Permanen {$deletedCount} entri data)",
            'data_lama'       => ['deleted_count' => $deletedCount],
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.restore-points.index', ['tab' => 'trash'])
            ->with('success', "Tempat sampah berhasil dikosongkan. Sebanyak {$deletedCount} data terhapus permanen.");
    }

    /**
     * Create a new database restore point snapshot.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ], [
            'nama.required' => 'Nama / Label Restore Point wajib diisi.',
        ]);

        try {
            $rp = $this->restoreService->createSnapshot(
                auth()->user(),
                $request->nama,
                $request->deskripsi,
                'manual'
            );

            return redirect()->route('admin.restore-points.index', ['tab' => 'snapshots'])
                ->with('success', "Restore Point '{$rp->nama}' (#{$rp->kode}) berhasil dibuat!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat Restore Point: ' . $e->getMessage());
        }
    }

    /**
     * Restore / Rollback database to selected restore point.
     */
    public function restore(Request $request, $id)
    {
        $request->validate([
            'password_confirmation' => 'required|string',
        ], [
            'password_confirmation.required' => 'Kata sandi konfirmasi Admin wajib diisi demi keamanan.',
        ]);

        if (!Hash::check($request->password_confirmation, auth()->user()->password)) {
            return back()->with('error', 'Konfirmasi Kata Sandi Admin tidak sesuai. Proses pemulihan data dibatalkan.');
        }

        $restorePoint = RestorePoint::findOrFail($id);

        try {
            $summary = $this->restoreService->restoreSnapshot($restorePoint, auth()->user());

            $totalRecords = array_sum($summary);
            return redirect()->route('admin.restore-points.index', ['tab' => 'snapshots'])
                ->with('success', "Sistem berhasil dipulihkan ke titik '{$restorePoint->nama}' ({$restorePoint->kode}). Total {$totalRecords} rekaman data telah disinkronkan.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memulihkan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Download snapshot backup file.
     */
    public function download($id)
    {
        $restorePoint = RestorePoint::findOrFail($id);
        $fullPath = storage_path('app/' . $restorePoint->file_path);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'File snapshot tidak ditemukan di server penyimpanan.');
        }

        $downloadName = 'SMKP-Snapshot-' . $restorePoint->kode . '.json';
        return response()->download($fullPath, $downloadName, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Delete a restore point.
     */
    public function destroy($id)
    {
        $restorePoint = RestorePoint::findOrFail($id);

        try {
            $nama = $restorePoint->nama;
            $kode = $restorePoint->kode;
            $this->restoreService->deleteSnapshot($restorePoint, auth()->user());

            return redirect()->route('admin.restore-points.index', ['tab' => 'snapshots'])
                ->with('success', "Restore Point '{$nama}' ({$kode}) berhasil dihapus beserta file snapshot fisiknya.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus Restore Point: ' . $e->getMessage());
        }
    }

    /**
     * Helper to resolve Eloquent Model class name from type.
     */
    protected function getModelClass(string $type): ?string
    {
        return match ($type) {
            'audit_sesi' => AuditSesi::class,
            'user'       => User::class,
            'perusahaan' => Perusahaan::class,
            'departemen' => Departemen::class,
            'elemen'     => Elemen::class,
            'sub_elemen' => SubElemen::class,
            'kriteria'   => Kriteria::class,
            default      => null,
        };
    }

    /**
     * Helper to resolve Eloquent Model instance.
     */
    protected function resolveModel(string $type, $id, bool $onlyTrashed = false)
    {
        $class = $this->getModelClass($type);
        if (!$class) return null;

        $query = $onlyTrashed ? $class::onlyTrashed() : $class::query();
        return $query->find($id);
    }

    /**
     * Helper to get user-friendly title of a model instance.
     */
    protected function getModelTitle(string $type, $model): string
    {
        return match ($type) {
            'audit_sesi' => "Sesi Audit #{$model->id}" . ($model->perusahaan ? " — {$model->perusahaan->nama_perusahaan}" : ""),
            'user'       => "{$model->name} ({$model->username})",
            'perusahaan' => $model->nama_perusahaan ?? "Perusahaan #{$model->id}",
            'departemen' => $model->nama_departemen ?? "Departemen #{$model->id}",
            'elemen'     => "Elemen {$model->nomor_elemen}: {$model->nama_elemen}",
            'sub_elemen' => "Sub-Elemen {$model->nomor_sub_elemen}: {$model->nama_sub_elemen}",
            'kriteria'   => "Kriteria {$model->nomor_kriteria}",
            default      => "Data #{$model->id}",
        };
    }
}
