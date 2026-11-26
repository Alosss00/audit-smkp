<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MasterFaktorPenyesuaian;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterFaktorPenyesuaianController extends Controller
{
    public function index(): View
    {
        $items = MasterFaktorPenyesuaian::orderBy('urutan')->orderBy('id')->get();
        $trashedItems = MasterFaktorPenyesuaian::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.master-faktor-penyesuaian.index', compact('items', 'trashedItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode'       => ['required', 'string', 'max:100', Rule::unique('master_faktor_penyesuaian', 'kode')],
            'nama'       => 'required|string|max:255',
            'tipe'       => 'required|in:tambah,kurang',
            'nilai_hari' => 'nullable|numeric|min:0|max:999.9',
            'urutan'     => 'nullable|integer|min:0|max:65535',
            'keterangan' => 'nullable|string|max:2000',
        ], [
            'kode.required'      => 'Kode faktor wajib diisi.',
            'kode.unique'        => 'Kode faktor sudah digunakan.',
            'nama.required'      => 'Nama faktor wajib diisi.',
            'tipe.required'      => 'Tipe faktor wajib dipilih.',
            'tipe.in'            => 'Tipe faktor harus tambah atau kurang.',
            'nilai_hari.numeric' => 'Nilai hari harus berupa angka.',
            'nilai_hari.min'     => 'Nilai hari minimal 0.',
        ]);

        $item = MasterFaktorPenyesuaian::create([
            'kode'       => strtoupper(trim($validated['kode'])),
            'nama'       => trim($validated['nama']),
            'tipe'       => $validated['tipe'],
            'nilai_hari' => $request->filled('nilai_hari') ? round((float) $validated['nilai_hari'], 1) : null,
            'urutan'     => (int) ($validated['urutan'] ?? 0),
            'is_active'  => $request->has('is_active') ? $request->boolean('is_active') : true,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Faktor Penyesuaian',
            'tindakan'        => "Menambahkan faktor penyesuaian: {$item->kode} - {$item->nama}",
            'data_lama'       => null,
            'data_baru'       => $item->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-faktor-penyesuaian.index')
            ->with('success', "Faktor penyesuaian '{$item->kode}' berhasil ditambahkan.");
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $item = MasterFaktorPenyesuaian::findOrFail($id);
        $oldData = $item->toArray();

        $validated = $request->validate([
            'kode'       => ['required', 'string', 'max:100', Rule::unique('master_faktor_penyesuaian', 'kode')->ignore($item->id)],
            'nama'       => 'required|string|max:255',
            'tipe'       => 'required|in:tambah,kurang',
            'nilai_hari' => 'nullable|numeric|min:0|max:999.9',
            'urutan'     => 'nullable|integer|min:0|max:65535',
            'keterangan' => 'nullable|string|max:2000',
        ], [
            'kode.required'      => 'Kode faktor wajib diisi.',
            'kode.unique'        => 'Kode faktor sudah digunakan.',
            'nama.required'      => 'Nama faktor wajib diisi.',
            'tipe.required'      => 'Tipe faktor wajib dipilih.',
            'tipe.in'            => 'Tipe faktor harus tambah atau kurang.',
            'nilai_hari.numeric' => 'Nilai hari harus berupa angka.',
            'nilai_hari.min'     => 'Nilai hari minimal 0.',
        ]);

        $item->update([
            'kode'       => strtoupper(trim($validated['kode'])),
            'nama'       => trim($validated['nama']),
            'tipe'       => $validated['tipe'],
            'nilai_hari' => $request->filled('nilai_hari') ? round((float) $validated['nilai_hari'], 1) : null,
            'urutan'     => (int) ($validated['urutan'] ?? 0),
            'is_active'  => $request->boolean('is_active'),
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Faktor Penyesuaian',
            'tindakan'        => "Memperbarui faktor penyesuaian: {$item->kode} - {$item->nama}",
            'data_lama'       => $oldData,
            'data_baru'       => $item->fresh()->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-faktor-penyesuaian.index')
            ->with('success', "Faktor penyesuaian '{$item->kode}' berhasil diperbarui.");
    }

    public function toggleActive(int|string $id): RedirectResponse
    {
        $item = MasterFaktorPenyesuaian::findOrFail($id);
        $old = $item->is_active;
        $item->update(['is_active' => ! $old]);

        $statusLabel = $item->is_active ? 'Aktif' : 'Nonaktif';
        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Faktor Penyesuaian',
            'tindakan'        => "Mengubah status faktor {$item->kode} menjadi {$statusLabel}",
            'data_lama'       => ['is_active' => $old],
            'data_baru'       => ['is_active' => $item->is_active],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-faktor-penyesuaian.index')
            ->with('success', "Status faktor '{$item->kode}' diubah menjadi {$statusLabel}.");
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $item = MasterFaktorPenyesuaian::findOrFail($id);
        $oldData = $item->toArray();
        $item->delete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Faktor Penyesuaian',
            'tindakan'        => "Menghapus (soft delete) faktor penyesuaian: {$oldData['kode']}",
            'data_lama'       => $oldData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-faktor-penyesuaian.index')
            ->with('success', "Faktor penyesuaian '{$oldData['kode']}' dipindahkan ke tempat sampah.");
    }

    public function restore(int|string $id): RedirectResponse
    {
        $item = MasterFaktorPenyesuaian::onlyTrashed()->findOrFail($id);
        $item->restore();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Faktor Penyesuaian',
            'tindakan'        => "Memulihkan faktor penyesuaian: {$item->kode}",
            'data_lama'       => null,
            'data_baru'       => $item->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-faktor-penyesuaian.index')
            ->with('success', "Faktor penyesuaian '{$item->kode}' berhasil dipulihkan.");
    }

    public function forceDelete(int|string $id): RedirectResponse
    {
        $item = MasterFaktorPenyesuaian::onlyTrashed()->findOrFail($id);
        $oldData = $item->toArray();
        $item->forceDelete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Faktor Penyesuaian',
            'tindakan'        => "Menghapus permanen faktor penyesuaian: {$oldData['kode']}",
            'data_lama'       => $oldData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-faktor-penyesuaian.index')
            ->with('success', "Faktor penyesuaian '{$oldData['kode']}' dihapus permanen.");
    }
}
