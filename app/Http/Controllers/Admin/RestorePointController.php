<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuditSesi;
use App\Models\Departemen;
use App\Models\Elemen;
use App\Models\Kriteria;
use App\Models\Perusahaan;
use App\Models\SubElemen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestorePointController extends Controller
{
    /**
     * Display listing of all soft-deleted application records (Recycle Bin / Pusat Pemulihan Data).
     */
    public function index(Request $request)
    {
        // 1. Aggregate All Soft-Deleted Data Across Application Modules
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
                'info'        => 'Role: ' . ($item->role_label ?? ucfirst($item->role)) . ' | Email: ' . ($item->email ?? '-'),
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
                'title'       => 'Elemen ' . ($item->kode_elemen ?? $item->nomor_elemen) . ': ' . $item->nama_elemen,
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
                'title'       => 'Sub-Elemen ' . ($item->kode_sub_elemen ?? $item->nomor_sub_elemen) . ': ' . $item->nama_sub_elemen,
                'info'        => 'Induk: Elemen ' . ($item->elemen->kode_elemen ?? $item->elemen->nomor_elemen ?? '-'),
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
                'title'       => 'Kriteria ' . ($item->nomor_kriteria ?? $item->kode_kriteria ?? '#') . ': ' . substr($item->deskripsi_kriteria ?? $item->pertanyaan, 0, 80) . '...',
                'info'        => 'Sub-Elemen: ' . ($item->subElemen->kode_sub_elemen ?? $item->subElemen->nomor_sub_elemen ?? '-'),
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
        if ($request->filled('trash_module') && $request->trash_module !== 'all') {
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
            'modul'           => 'Pusat Pemulihan Data',
            'tindakan'        => "Memulihkan data terhapus ({$type}): {$title}",
            'data_lama'       => null,
            'data_baru'       => ['type' => $type, 'id' => $id, 'title' => $title],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.restore-points.index')
            ->with('success', "Data '{$title}' berhasil dipulihkan kembali ke aplikasi!");
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
            'modul'           => 'Pusat Pemulihan Data',
            'tindakan'        => "Menghapus PERMANEN data ({$type}): {$title}",
            'data_lama'       => ['type' => $type, 'id' => $id, 'title' => $title],
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.restore-points.index')
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
            'modul'           => 'Pusat Pemulihan Data',
            'tindakan'        => "Memulihkan SEMUA data terhapus ({$restoredCount} entri)",
            'data_lama'       => null,
            'data_baru'       => ['restored_count' => $restoredCount, 'module' => $module],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.restore-points.index')
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
            'password_confirmation.required' => 'Kata sandi Administrator wajib diisi untuk mengosongkan kotak sampah.',
        ]);

        if (!Hash::check($request->password_confirmation, auth()->user()->password)) {
            return back()->with('error', 'Konfirmasi Kata Sandi Administrator tidak sesuai. Pengosongan data dibatalkan.');
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
            'modul'           => 'Pusat Pemulihan Data',
            'tindakan'        => "Mengosongkan kotak sampah (Hapus Permanen {$deletedCount} entri data)",
            'data_lama'       => ['deleted_count' => $deletedCount],
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.restore-points.index')
            ->with('success', "Kotak sampah berhasil dikosongkan. Sebanyak {$deletedCount} data terhapus permanen.");
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
            'elemen'     => "Elemen " . ($model->kode_elemen ?? $model->nomor_elemen) . ": {$model->nama_elemen}",
            'sub_elemen' => "Sub-Elemen " . ($model->kode_sub_elemen ?? $model->nomor_sub_elemen) . ": {$model->nama_sub_elemen}",
            'kriteria'   => "Kriteria " . ($model->nomor_kriteria ?? $model->kode_kriteria ?? '#'),
            default      => "Data #{$model->id}",
        };
    }
}
