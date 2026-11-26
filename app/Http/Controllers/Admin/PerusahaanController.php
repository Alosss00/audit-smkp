<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of perusahaans.
     */
    public function index(Request $request): View
    {
        $query = Perusahaan::with('auditSesis')->withCount('auditSesis');

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
        $trashedPerusahaans = Perusahaan::onlyTrashed()->with('auditSesis')->withCount('auditSesis')->latest('deleted_at')->get();

        return view('admin.perusahaans.index', compact('perusahaans', 'trashedPerusahaans'));
    }

    /**
     * Store a newly created perusahaan.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_perusahaan'       => ['required', 'string', 'max:255', Rule::unique('perusahaans', 'nama_perusahaan')->whereNull('deleted_at')],
            'kode_perusahaan'       => 'nullable|string|max:50',
            'kategori'              => 'required|in:Pemegang IUP,Kontraktor,Subkontraktor,Lainnya',
            'jenis_usaha'           => ['nullable', Rule::in(array_keys(Perusahaan::JENIS_USAHA_OPTIONS))],
            'nama_ktt_default'      => 'nullable|string|max:255',
            'jenis_perizinan'       => 'nullable|string',
            'jenis_komoditas'       => 'nullable|string|max:255',
            'alamat_kantor_site'    => 'nullable|string',
            'alamat_lokasi_tambang' => 'nullable|string',
            'alamat_kantor_pusat'   => 'nullable|string',
        ], [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'nama_perusahaan.unique'   => 'Nama perusahaan ini sudah terdaftar dalam sistem.',
            'kategori.required'        => 'Kategori perusahaan wajib dipilih.',
            'kategori.in'              => 'Kategori perusahaan tidak valid.',
            'jenis_usaha.in'           => 'Klasifikasi jenis usaha tidak valid.',
        ]);

        $perusahaan = Perusahaan::create([
            'nama_perusahaan'       => $request->nama_perusahaan,
            'kode_perusahaan'       => $request->kode_perusahaan,
            'kategori'              => $request->kategori,
            'jenis_usaha'           => $request->jenis_usaha ?: null,
            'nama_ktt_default'      => $request->nama_ktt_default,
            'jenis_perizinan'       => $request->jenis_perizinan,
            'jenis_komoditas'       => $request->jenis_komoditas,
            'alamat_kantor_site'    => $request->alamat_kantor_site,
            'alamat_lokasi_tambang' => $request->alamat_lokasi_tambang,
            'alamat_kantor_pusat'   => $request->alamat_kantor_pusat,
            'is_active'             => true,
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
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $oldData = $perusahaan->toArray();

        $request->validate([
            'nama_perusahaan'       => ['required', 'string', 'max:255', Rule::unique('perusahaans', 'nama_perusahaan')->ignore($perusahaan->id)->whereNull('deleted_at')],
            'kode_perusahaan'       => 'nullable|string|max:50',
            'kategori'              => 'required|in:Pemegang IUP,Kontraktor,Subkontraktor,Lainnya',
            'jenis_usaha'           => ['nullable', Rule::in(array_keys(Perusahaan::JENIS_USAHA_OPTIONS))],
            'nama_ktt_default'      => 'nullable|string|max:255',
            'jenis_perizinan'       => 'nullable|string',
            'jenis_komoditas'       => 'nullable|string|max:255',
            'alamat_kantor_site'    => 'nullable|string',
            'alamat_lokasi_tambang' => 'nullable|string',
            'alamat_kantor_pusat'   => 'nullable|string',
        ], [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'nama_perusahaan.unique'   => 'Nama perusahaan ini sudah terdaftar dalam sistem.',
            'kategori.required'        => 'Kategori perusahaan wajib dipilih.',
            'kategori.in'              => 'Kategori perusahaan tidak valid.',
            'jenis_usaha.in'           => 'Klasifikasi jenis usaha tidak valid.',
        ]);

        $perusahaan->update([
            'nama_perusahaan'       => $request->nama_perusahaan,
            'kode_perusahaan'       => $request->kode_perusahaan,
            'kategori'              => $request->kategori,
            'jenis_usaha'           => $request->jenis_usaha ?: null,
            'nama_ktt_default'      => $request->nama_ktt_default,
            'jenis_perizinan'       => $request->jenis_perizinan,
            'jenis_komoditas'       => $request->jenis_komoditas,
            'alamat_kantor_site'    => $request->alamat_kantor_site,
            'alamat_lokasi_tambang' => $request->alamat_lokasi_tambang,
            'alamat_kantor_pusat'   => $request->alamat_kantor_pusat,
            'is_active'             => $request->has('is_active') ? true : false,
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
    public function toggleStatus(int|string $id): RedirectResponse
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
    public function destroy(int|string $id): RedirectResponse
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
    public function restore(int|string $id): RedirectResponse
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
    public function forceDelete(int|string $id): RedirectResponse
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
