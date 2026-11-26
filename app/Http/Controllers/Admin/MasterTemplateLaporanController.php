<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MasterTemplateLaporan;
use App\Services\LaporanTemplateRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MasterTemplateLaporanController extends Controller
{
    public function index(): View
    {
        $items = MasterTemplateLaporan::with('verifier')->orderBy('id')->get();
        $allowedPlaceholders = LaporanTemplateRenderer::ALLOWED_PLACEHOLDERS;

        return view('admin.master-laporan.template', compact('items', 'allowedPlaceholders'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $item = MasterTemplateLaporan::findOrFail($id);
        $old = $item->toArray();

        $validated = $request->validate([
            'judul'        => 'required|string|max:150',
            'isi_template' => 'required|string|max:20000',
        ], [
            'judul.required'        => 'Judul template wajib diisi.',
            'isi_template.required' => 'Isi template narasi wajib diisi.',
        ]);

        $item->update([
            'judul'        => trim($validated['judul']),
            'isi_template' => trim($validated['isi_template']),
            'is_active'    => $request->boolean('is_active', true),
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Template Laporan',
            'tindakan'        => "Memperbarui template laporan bagian '{$item->bagian}'",
            'data_lama'       => $old,
            'data_baru'       => $item->fresh()->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-template-laporan.index')
            ->with('success', "Template '{$item->judul}' berhasil diperbarui.");
    }

    public function toggleVerify(int|string $id): RedirectResponse
    {
        $item = MasterTemplateLaporan::findOrFail($id);
        $oldVerified = $item->is_verified;

        $item->update([
            'is_verified' => ! $oldVerified,
            'verified_by' => ! $oldVerified ? auth()->id() : null,
            'verified_at' => ! $oldVerified ? now() : null,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Template Laporan',
            'tindakan'        => (! $oldVerified ? 'Memverifikasi' : 'Membatalkan verifikasi') . " template bagian '{$item->bagian}'",
            'data_lama'       => ['is_verified' => $oldVerified],
            'data_baru'       => ['is_verified' => ! $oldVerified],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-template-laporan.index')
            ->with('success', 'Status verifikasi template berhasil diperbarui.');
    }
}
