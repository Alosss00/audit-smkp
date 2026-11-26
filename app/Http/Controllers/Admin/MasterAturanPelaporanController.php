<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MasterAturanPelaporan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterAturanPelaporanController extends Controller
{
    public function index(): View
    {
        $items = MasterAturanPelaporan::with('verifier')->orderBy('id')->get();

        return view('admin.master-laporan.aturan-pelaporan', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode'                       => ['required', 'string', 'max:60', Rule::unique('master_aturan_pelaporan', 'kode')->whereNull('deleted_at')],
            'nama_aturan'                => 'required|string|max:150',
            'hari_setelah_akhir_periode' => 'nullable|integer|min:1|max:365',
            'keterangan'                 => 'nullable|string|max:5000',
        ], [
            'kode.required'        => 'Kode aturan pelaporan wajib diisi.',
            'kode.unique'          => 'Kode aturan pelaporan sudah digunakan.',
            'nama_aturan.required' => 'Nama aturan pelaporan wajib diisi.',
        ]);

        $item = MasterAturanPelaporan::create([
            'kode'                       => strtoupper(trim($validated['kode'])),
            'nama_aturan'                => trim($validated['nama_aturan']),
            'hari_setelah_akhir_periode' => $validated['hari_setelah_akhir_periode'] ?? null,
            'keterangan'                 => isset($validated['keterangan']) && trim((string) $validated['keterangan']) !== '' ? trim((string) $validated['keterangan']) : null,
            'is_active'                  => true,
            'is_verified'                => false,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Aturan Pelaporan',
            'tindakan'        => "Menambahkan aturan pelaporan: {$item->nama_aturan}",
            'data_lama'       => null,
            'data_baru'       => $item->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-aturan-pelaporan.index')
            ->with('success', "Aturan pelaporan '{$item->nama_aturan}' berhasil ditambahkan.");
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $item = MasterAturanPelaporan::findOrFail($id);
        $old = $item->toArray();

        $validated = $request->validate([
            'kode'                       => ['required', 'string', 'max:60', Rule::unique('master_aturan_pelaporan', 'kode')->ignore($item->id)->whereNull('deleted_at')],
            'nama_aturan'                => 'required|string|max:150',
            'hari_setelah_akhir_periode' => 'nullable|integer|min:1|max:365',
            'keterangan'                 => 'nullable|string|max:5000',
        ], [
            'kode.required'        => 'Kode aturan pelaporan wajib diisi.',
            'kode.unique'          => 'Kode aturan pelaporan sudah digunakan.',
            'nama_aturan.required' => 'Nama aturan pelaporan wajib diisi.',
        ]);

        $item->update([
            'kode'                       => strtoupper(trim($validated['kode'])),
            'nama_aturan'                => trim($validated['nama_aturan']),
            'hari_setelah_akhir_periode' => $validated['hari_setelah_akhir_periode'] ?? null,
            'keterangan'                 => isset($validated['keterangan']) && trim((string) $validated['keterangan']) !== '' ? trim((string) $validated['keterangan']) : null,
            'is_active'                  => $request->boolean('is_active', true),
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Aturan Pelaporan',
            'tindakan'        => "Memperbarui aturan pelaporan: {$item->nama_aturan}",
            'data_lama'       => $old,
            'data_baru'       => $item->fresh()->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-aturan-pelaporan.index')
            ->with('success', "Aturan pelaporan '{$item->nama_aturan}' berhasil diperbarui.");
    }

    public function toggleVerify(int|string $id): RedirectResponse
    {
        $item = MasterAturanPelaporan::findOrFail($id);
        $oldVerified = $item->is_verified;

        $item->update([
            'is_verified' => ! $oldVerified,
            'verified_by' => ! $oldVerified ? auth()->id() : null,
            'verified_at' => ! $oldVerified ? now() : null,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Aturan Pelaporan',
            'tindakan'        => (! $oldVerified ? 'Memverifikasi' : 'Membatalkan verifikasi') . " aturan pelaporan: {$item->nama_aturan}",
            'data_lama'       => ['is_verified' => $oldVerified],
            'data_baru'       => ['is_verified' => ! $oldVerified],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-aturan-pelaporan.index')
            ->with('success', 'Status verifikasi aturan pelaporan berhasil diperbarui.');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $item = MasterAturanPelaporan::findOrFail($id);
        $old = $item->toArray();
        $label = $item->nama_aturan;
        $item->delete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Aturan Pelaporan',
            'tindakan'        => "Menghapus (Soft Delete) aturan pelaporan: {$label}",
            'data_lama'       => $old,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-aturan-pelaporan.index')
            ->with('success', "Aturan pelaporan '{$label}' berhasil dihapus.");
    }
}
