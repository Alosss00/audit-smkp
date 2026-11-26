<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MasterDasarHukum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterDasarHukumController extends Controller
{
    public function index(): View
    {
        $items = MasterDasarHukum::with('verifier')->orderBy('urutan')->orderBy('id')->get();

        return view('admin.master-laporan.dasar-hukum', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode'            => ['required', 'string', 'max:60', Rule::unique('master_dasar_hukum', 'kode')->whereNull('deleted_at')],
            'nomor_peraturan' => 'required|string|max:150',
            'judul'           => 'required|string|max:300',
            'ringkasan'       => 'nullable|string|max:5000',
            'urutan'          => 'nullable|integer|min:0|max:999',
        ], [
            'kode.required'            => 'Kode dasar hukum wajib diisi.',
            'kode.unique'              => 'Kode dasar hukum sudah digunakan.',
            'nomor_peraturan.required' => 'Nomor peraturan wajib diisi.',
            'judul.required'           => 'Judul peraturan wajib diisi.',
        ]);

        $item = MasterDasarHukum::create([
            'kode'            => strtoupper(trim($validated['kode'])),
            'nomor_peraturan' => trim($validated['nomor_peraturan']),
            'judul'           => trim($validated['judul']),
            'ringkasan'       => isset($validated['ringkasan']) && trim((string) $validated['ringkasan']) !== '' ? trim((string) $validated['ringkasan']) : null,
            'urutan'          => (int) ($validated['urutan'] ?? 0),
            'is_active'       => true,
            'is_verified'     => false,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Dasar Hukum',
            'tindakan'        => "Menambahkan dasar hukum: {$item->nomor_peraturan}",
            'data_lama'       => null,
            'data_baru'       => $item->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-dasar-hukum.index')
            ->with('success', "Dasar hukum '{$item->nomor_peraturan}' berhasil ditambahkan.");
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $item = MasterDasarHukum::findOrFail($id);
        $old = $item->toArray();

        $validated = $request->validate([
            'kode'            => ['required', 'string', 'max:60', Rule::unique('master_dasar_hukum', 'kode')->ignore($item->id)->whereNull('deleted_at')],
            'nomor_peraturan' => 'required|string|max:150',
            'judul'           => 'required|string|max:300',
            'ringkasan'       => 'nullable|string|max:5000',
            'urutan'          => 'nullable|integer|min:0|max:999',
        ], [
            'kode.required'            => 'Kode dasar hukum wajib diisi.',
            'kode.unique'              => 'Kode dasar hukum sudah digunakan.',
            'nomor_peraturan.required' => 'Nomor peraturan wajib diisi.',
            'judul.required'           => 'Judul peraturan wajib diisi.',
        ]);

        $item->update([
            'kode'            => strtoupper(trim($validated['kode'])),
            'nomor_peraturan' => trim($validated['nomor_peraturan']),
            'judul'           => trim($validated['judul']),
            'ringkasan'       => isset($validated['ringkasan']) && trim((string) $validated['ringkasan']) !== '' ? trim((string) $validated['ringkasan']) : null,
            'urutan'          => (int) ($validated['urutan'] ?? 0),
            'is_active'       => $request->boolean('is_active', true),
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Dasar Hukum',
            'tindakan'        => "Memperbarui dasar hukum: {$item->nomor_peraturan}",
            'data_lama'       => $old,
            'data_baru'       => $item->fresh()->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-dasar-hukum.index')
            ->with('success', "Dasar hukum '{$item->nomor_peraturan}' berhasil diperbarui.");
    }

    public function toggleVerify(int|string $id): RedirectResponse
    {
        $item = MasterDasarHukum::findOrFail($id);
        $oldVerified = $item->is_verified;

        $item->update([
            'is_verified' => ! $oldVerified,
            'verified_by' => ! $oldVerified ? auth()->id() : null,
            'verified_at' => ! $oldVerified ? now() : null,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Dasar Hukum',
            'tindakan'        => (! $oldVerified ? 'Memverifikasi' : 'Membatalkan verifikasi') . " dasar hukum: {$item->nomor_peraturan}",
            'data_lama'       => ['is_verified' => $oldVerified],
            'data_baru'       => ['is_verified' => ! $oldVerified],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-dasar-hukum.index')
            ->with('success', 'Status verifikasi dasar hukum berhasil diperbarui.');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $item = MasterDasarHukum::findOrFail($id);
        $old = $item->toArray();
        $label = $item->nomor_peraturan;
        $item->delete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Dasar Hukum',
            'tindakan'        => "Menghapus (Soft Delete) dasar hukum: {$label}",
            'data_lama'       => $old,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-dasar-hukum.index')
            ->with('success', "Dasar hukum '{$label}' berhasil dihapus.");
    }
}
