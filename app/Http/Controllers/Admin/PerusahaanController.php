<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of perusahaans.
     */
    public function index(Request $request)
    {
        $query = Perusahaan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                  ->orWhere('kode_perusahaan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $perusahaans = $query->orderBy('nama_perusahaan')->get();
        $trashedPerusahaans = Perusahaan::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.perusahaans.index', compact('perusahaans', 'trashedPerusahaans'));
    }

    /**
     * Store a newly created perusahaan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255|unique:perusahaans,nama_perusahaan',
            'kode_perusahaan' => 'nullable|string|max:50',
            'kategori'        => 'required|in:Pemegang IUP,Kontraktor,Subkontraktor,Lainnya',
        ], [
            'nama_perusahaan.unique' => 'Nama perusahaan ini sudah terdaftar dalam sistem.',
        ]);

        $perusahaan = Perusahaan::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'kode_perusahaan' => $request->kode_perusahaan,
            'kategori'        => $request->kategori,
            'is_active'       => true,
        ]);

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Perusahaan',
            'tindakan'        => "Menambahkan perusahaan baru: {$perusahaan->nama_perusahaan}",
            'data_lama'       => null,
            'data_baru'       => $perusahaan->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.perusahaans.index')
            ->with('success', "Perusahaan '{$request->nama_perusahaan}' berhasil ditambahkan!");
    }

    /**
     * Update the specified perusahaan.
     */
    public function update(Request $request, $id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $oldData = $perusahaan->toArray();

        $request->validate([
            'nama_perusahaan' => 'required|string|max:255|unique:perusahaans,nama_perusahaan,' . $perusahaan->id,
            'kode_perusahaan' => 'nullable|string|max:50',
            'kategori'        => 'required|in:Pemegang IUP,Kontraktor,Subkontraktor,Lainnya',
        ]);

        $perusahaan->update([
            'nama_perusahaan' => $request->nama_perusahaan,
            'kode_perusahaan' => $request->kode_perusahaan,
            'kategori'        => $request->kategori,
            'is_active'       => $request->has('is_active') ? true : false,
        ]);

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Perusahaan',
            'tindakan'        => "Mengubah data perusahaan: {$perusahaan->nama_perusahaan}",
            'data_lama'       => $oldData,
            'data_baru'       => $perusahaan->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.perusahaans.index')
            ->with('success', "Data perusahaan '{$perusahaan->nama_perusahaan}' berhasil diperbarui!");
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $perusahaan->is_active = !$perusahaan->is_active;
        $perusahaan->save();

        $statusText = $perusahaan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Perusahaan',
            'tindakan'        => "Mengubah status perusahaan '{$perusahaan->nama_perusahaan}': {$statusText}",
            'data_lama'       => ['is_active' => !$perusahaan->is_active],
            'data_baru'       => ['is_active' => $perusahaan->is_active],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.perusahaans.index')
            ->with('success', "Status perusahaan '{$perusahaan->nama_perusahaan}' berhasil {$statusText}.");
    }

    /**
     * Remove the specified perusahaan (Soft Delete).
     */
    public function destroy($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $name = $perusahaan->nama_perusahaan;
        $oldData = $perusahaan->toArray();
        $perusahaan->delete();

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Perusahaan',
            'tindakan'        => "Menonaktifkan (Soft Delete) Perusahaan: {$name}",
            'data_lama'       => $oldData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.perusahaans.index')
            ->with('success', "Perusahaan '{$name}' berhasil dinonaktifkan (Soft Delete).");
    }

    /**
     * Restore soft-deleted perusahaan.
     */
    public function restore($id)
    {
        $perusahaan = Perusahaan::onlyTrashed()->findOrFail($id);
        $name = $perusahaan->nama_perusahaan;
        $perusahaan->restore();

        \App\Models\AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Perusahaan',
            'tindakan'        => "Memulihkan (Restore Point) Perusahaan: {$name}",
            'data_lama'       => null,
            'data_baru'       => $perusahaan->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.perusahaans.index')
            ->with('success', "Perusahaan '{$name}' berhasil dipulihkan!");
    }

    /**
     * Permanently delete perusahaan.
     */
    public function forceDelete($id)
    {
        $perusahaan = Perusahaan::onlyTrashed()->findOrFail($id);
        $name = $perusahaan->nama_perusahaan;
        $oldData = $perusahaan->toArray();

        try {
            $perusahaan->forceDelete();

            \App\Models\AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Master Perusahaan',
                'tindakan'        => "Menghapus Permanen (Force Delete) Perusahaan: {$name}",
                'data_lama'       => $oldData,
                'data_baru'       => null,
                'waktu_perubahan' => now(),
            ]);

            return redirect()->route('admin.perusahaans.index')
                ->with('success', "Perusahaan '{$name}' berhasil dihapus permanen!");
        } catch (\Exception $e) {
            return redirect()->route('admin.perusahaans.index')
                ->with('error', "Gagal menghapus permanen: Perusahaan terkait dengan data audit yang ada.");
        }
    }
}
