<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\SubElemen;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KriteriaController extends Controller
{
    /**
     * Display a listing of criteria (active & trashed).
     */
    public function index()
    {
        $kriterias = Kriteria::with(['subElemen.elemen', 'dependency'])->latest()->get();
        $trashedKriterias = Kriteria::onlyTrashed()->with(['subElemen.elemen'])->latest()->get();
        $subElemens = SubElemen::with('elemen')->orderBy('kode_sub')->get();

        return view('admin.kriterias.index', compact('kriterias', 'trashedKriterias', 'subElemens'));
    }

    /**
     * Check if assigning newDependencyId to targetKriteria creates a dependency cycle.
     */
    public function wouldCreateCycle(?int $targetKriteriaId, ?int $newDependencyId): bool
    {
        if (!$targetKriteriaId || !$newDependencyId) {
            return false;
        }

        if ($targetKriteriaId === $newDependencyId) {
            return true;
        }

        $visited = [$targetKriteriaId];
        $currentId = $newDependencyId;

        while ($currentId !== null) {
            if (in_array($currentId, $visited)) {
                return true; // Siklus terdeteksi
            }
            $visited[] = $currentId;
            $currentId = Kriteria::find($currentId)?->dependency_id;
        }

        return false;
    }

    /**
     * Store a newly created criteria (or multiple criteria in batch) in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_elemen_id' => 'required|exists:sub_elemens,id',
        ], [
            'sub_elemen_id.required' => 'Induk Sub-Elemen wajib dipilih.',
        ]);

        $subElemen = SubElemen::findOrFail($request->sub_elemen_id);

        // Check if multiple sub-sub elements (batch) are provided
        if ($request->has('sub_subs') && is_array($request->input('sub_subs'))) {
            $subSubs = $request->input('sub_subs');
            $createdCount = 0;

            // Remove existing standalone default Kriteria if we are adding explicit child Kriterias
            $standaloneKriteria = $subElemen->kriterias()->where('kode_kriteria', $subElemen->kode_sub)->first();
            if ($standaloneKriteria) {
                $standaloneKriteria->delete();
            }

            foreach ($subSubs as $idx => $item) {
                $deskripsi = trim($item['deskripsi'] ?? '');
                $kode = trim($item['kode_kriteria'] ?? '');

                // Skip completely empty row if any
                if ($deskripsi === '' && $kode === '' && !isset($item['nilai_maksimal'])) {
                    continue;
                }

                $explicitCount = $subElemen->kriterias()->where('kode_kriteria', '!=', $subElemen->kode_sub)->count();
                $kodeKriteria = $kode !== ''
                    ? $kode
                    : $subElemen->kode_sub . '.' . ($explicitCount + 1);

                $nilaiMaksimal = isset($item['nilai_maksimal']) && is_numeric($item['nilai_maksimal'])
                    ? (float) $item['nilai_maksimal']
                    : ((float) $subElemen->nilai_maksimal > 0 ? (float) $subElemen->nilai_maksimal : 4.0);

                $deskripsiVal = $deskripsi !== '' ? $deskripsi : $subElemen->nama_sub;

                // Process rubric guidelines
                $pedomanArray = [];
                if (isset($item['pedoman_nilai']) && is_array($item['pedoman_nilai'])) {
                    $pedomanArray = $item['pedoman_nilai'];
                }

                $data = [
                    'sub_elemen_id'       => $subElemen->id,
                    'kode_kriteria'       => $kodeKriteria,
                    'deskripsi'           => $deskripsiVal,
                    'nilai_maksimal'      => $nilaiMaksimal,
                    'persyaratan_dokumen' => $item['persyaratan_dokumen'] ?? null,
                    'pedoman_nilai_0'     => $pedomanArray['0'] ?? null,
                    'pedoman_nilai_1'     => $pedomanArray['1'] ?? null,
                    'pedoman_nilai_2'     => $pedomanArray['2'] ?? null,
                    'pedoman_nilai_3'     => $pedomanArray['3'] ?? null,
                    'pedoman_nilai_4'     => $pedomanArray['4'] ?? null,
                    'pedoman_nilai_json'  => !empty($pedomanArray) ? $pedomanArray : null,
                    'is_na'               => !empty($item['is_na']),
                ];

                $trashedKriteria = Kriteria::onlyTrashed()
                    ->where('sub_elemen_id', $subElemen->id)
                    ->where('kode_kriteria', $kodeKriteria)
                    ->first();

                if ($trashedKriteria) {
                    $trashedKriteria->restore();
                    $trashedKriteria->update($data);
                    $kriteria = $trashedKriteria;
                } else {
                    $kriteria = Kriteria::create($data);
                }

                AuditLog::create([
                    'user_id'         => auth()->id(),
                    'modul'           => 'Master Kriteria',
                    'tindakan'        => "Membuat/Memperbarui Sub-sub Elemen (Kriteria): {$kriteria->kode_kriteria}",
                    'data_lama'       => null,
                    'data_baru'       => $kriteria->toArray(),
                    'waktu_perubahan' => now(),
                ]);

                $createdCount++;
            }

            $subElemen->syncNilaiMaksimalFromKriterias();

            return redirect()->back()
                ->with('success', "Berhasil menambahkan {$createdCount} Sub-sub Elemen (Kriteria)!");
        }

        // Single item fallback for backward compatibility
        $request->validate([
            'kode_kriteria'       => 'nullable|string|max:50',
            'deskripsi'           => 'nullable|string',
            'nilai_maksimal'      => 'nullable|numeric|min:0|max:100',
            'persyaratan_dokumen' => 'nullable|string',
            'dependency_id'       => 'nullable|exists:kriterias,id',
            'dependency_note'     => 'nullable|string',
        ]);

        // Remove existing standalone default Kriteria if we are adding explicit child Kriterias
        $standaloneKriteria = $subElemen->kriterias()->where('kode_kriteria', $subElemen->kode_sub)->first();
        if ($standaloneKriteria && ($request->filled('kode_kriteria') && $request->kode_kriteria !== $subElemen->kode_sub)) {
            $standaloneKriteria->delete();
        }

        // Auto-generate code, max score, description if missing
        $explicitCount = $subElemen->kriterias()->where('kode_kriteria', '!=', $subElemen->kode_sub)->count();
        $kodeKriteria = $request->filled('kode_kriteria')
            ? $request->kode_kriteria
            : $subElemen->kode_sub . '.' . ($explicitCount + 1);

        $nilaiMaksimal = $request->filled('nilai_maksimal')
            ? (float) $request->nilai_maksimal
            : ((float) $subElemen->nilai_maksimal > 0 ? (float) $subElemen->nilai_maksimal : 4.0);

        $deskripsi = $request->filled('deskripsi')
            ? $request->deskripsi
            : $subElemen->nama_sub;

        // Process rubric guidelines (0..N)
        $pedomanArray = [];
        if ($request->has('pedoman_nilai') && is_array($request->pedoman_nilai)) {
            $pedomanArray = $request->pedoman_nilai;
        } else {
            $maxScoreInt = (int) ceil($nilaiMaksimal);
            for ($i = 0; $i <= $maxScoreInt; $i++) {
                $key = "pedoman_nilai_{$i}";
                if ($request->has($key) && filled($request->input($key))) {
                    $pedomanArray[(string)$i] = $request->input($key);
                }
            }
        }

        $data = [
            'sub_elemen_id'       => $subElemen->id,
            'kode_kriteria'       => $kodeKriteria,
            'deskripsi'           => $deskripsi,
            'nilai_maksimal'      => $nilaiMaksimal,
            'persyaratan_dokumen' => $request->persyaratan_dokumen,
            'pedoman_nilai_0'     => $pedomanArray['0'] ?? $request->pedoman_nilai_0,
            'pedoman_nilai_1'     => $pedomanArray['1'] ?? $request->pedoman_nilai_1,
            'pedoman_nilai_2'     => $pedomanArray['2'] ?? $request->pedoman_nilai_2,
            'pedoman_nilai_3'     => $pedomanArray['3'] ?? $request->pedoman_nilai_3,
            'pedoman_nilai_4'     => $pedomanArray['4'] ?? $request->pedoman_nilai_4,
            'pedoman_nilai_json'  => !empty($pedomanArray) ? $pedomanArray : null,
            'dependency_id'       => $request->dependency_id,
            'dependency_note'     => $request->dependency_note,
            'is_na'               => $request->has('is_na') ? true : false,
        ];

        $trashedKriteria = Kriteria::onlyTrashed()
            ->where('sub_elemen_id', $subElemen->id)
            ->where('kode_kriteria', $kodeKriteria)
            ->first();

        if ($trashedKriteria) {
            $trashedKriteria->restore();
            $trashedKriteria->update($data);
            $kriteria = $trashedKriteria;
        } else {
            $kriteria = Kriteria::create($data);
        }

        $subElemen->syncNilaiMaksimalFromKriterias();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Kriteria',
            'tindakan'        => "Membuat/Memperbarui Sub-sub Elemen (Kriteria): {$kriteria->kode_kriteria}",
            'data_lama'       => null,
            'data_baru'       => $kriteria->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Sub-sub Elemen (Kriteria) baru berhasil disimpan!');
    }

    /**
     * Update the specified criteria in storage.
     */
    public function update(Request $request, $id)
    {
        $kriteria = Kriteria::findOrFail($id);

        $request->validate([
            'sub_elemen_id'       => 'nullable|exists:sub_elemens,id',
            'kode_kriteria'       => 'nullable|string|max:50',
            'deskripsi'           => 'nullable|string',
            'nilai_maksimal'      => 'nullable|numeric|min:0|max:100',
            'persyaratan_dokumen' => 'nullable|string',
            'dependency_id'       => 'nullable|exists:kriterias,id',
            'dependency_note'     => 'nullable|string',
        ]);

        if ($request->filled('dependency_id')) {
            $newDepId = (int) $request->dependency_id;
            if ($this->wouldCreateCycle($kriteria->id, $newDepId)) {
                return back()->withInput()->withErrors([
                    'dependency_id' => 'Kriteria prasyarat yang dipilih akan membentuk siklus dependensi. Pilih kriteria lain.'
                ]);
            }
        }

        $oldSubElemenId = $kriteria->sub_elemen_id;
        $originalData   = $kriteria->toArray();

        $data = $request->except(['_token', '_method']);
        $data['sub_elemen_id']  = $request->input('sub_elemen_id', $kriteria->sub_elemen_id);
        $data['kode_kriteria']  = $request->input('kode_kriteria', $kriteria->kode_kriteria);
        $data['deskripsi']      = $request->input('deskripsi', $kriteria->deskripsi);
        $data['nilai_maksimal'] = $request->input('nilai_maksimal', $kriteria->nilai_maksimal);
        $data['is_na']          = $request->has('is_na') ? true : ($request->has('from_edit_modal') ? false : $kriteria->is_na);

        // Process rubric guidelines array if sent
        if ($request->has('pedoman_nilai') && is_array($request->pedoman_nilai)) {
            $data['pedoman_nilai_json'] = $request->pedoman_nilai;
        }

        $kriteria->update($data);

        // Sync parent SubElemen total score to match sum of child kriterias
        if ($kriteria->subElemen) {
            $kriteria->subElemen->syncNilaiMaksimalFromKriterias();
        }
        if ($oldSubElemenId != $kriteria->sub_elemen_id) {
            $oldSub = SubElemen::find($oldSubElemenId);
            if ($oldSub) {
                $oldSub->syncNilaiMaksimalFromKriterias();
            }
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Kriteria',
            'tindakan'        => "Mengubah Rubrik Sub-sub Elemen (Kriteria): {$kriteria->kode_kriteria}",
            'data_lama'       => $originalData,
            'data_baru'       => $kriteria->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Rubrik Pedoman Penilaian berhasil diperbarui!');
    }

    /**
     * Toggle N/A (Not Applicable) status for a Sub-sub Elemen (Kriteria).
     */
    public function toggleNa($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $newNaState = !$kriteria->is_na;

        $kriteria->update(['is_na' => $newNaState]);

        $statusText = $newNaState ? 'di-set N/A (Not Applicable)' : 'diaktifkan kembali (Aktif)';

        return redirect()->back()
            ->with('success', "Sub-sub Elemen {$kriteria->kode_kriteria} berhasil {$statusText}!");
    }

    /**
     * Remove the specified criteria from storage.
     */
    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteriaData = $kriteria->toArray();
        $subElemen = $kriteria->subElemen;

        $kriteria->delete();

        if ($subElemen) {
            $subElemen->syncDefaultKriteria();
            $subElemen->syncNilaiMaksimalFromKriterias();
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Kriteria',
            'tindakan'        => "Menonaktifkan (Soft Delete) Sub-sub Elemen (Kriteria): {$kriteriaData['kode_kriteria']} - {$kriteriaData['deskripsi']}",
            'data_lama'       => $kriteriaData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Sub-sub Elemen berhasil dinonaktifkan (Soft Delete).');
    }

    /**
     * Restore soft-deleted criteria.
     */
    public function restore($id)
    {
        $kriteria = Kriteria::onlyTrashed()->findOrFail($id);
        $kriteria->restore();

        if ($kriteria->subElemen) {
            $kriteria->subElemen->syncNilaiMaksimalFromKriterias();
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Kriteria',
            'tindakan'        => "Memulihkan (Restore Point) Sub-sub Elemen: {$kriteria->kode_kriteria} - {$kriteria->deskripsi}",
            'data_lama'       => null,
            'data_baru'       => $kriteria->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Sub-sub Elemen berhasil diaktifkan kembali!');
    }

    /**
     * Permanently delete criteria.
     */
    public function forceDelete($id)
    {
        $kriteria = Kriteria::onlyTrashed()->findOrFail($id);
        $kriteriaData = $kriteria->toArray();

        try {
            $kriteria->forceDelete();

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Master Kriteria',
                'tindakan'        => "Menghapus Permanen (Force Delete) Sub-sub Elemen: {$kriteriaData['kode_kriteria']}",
                'data_lama'       => $kriteriaData,
                'data_baru'       => null,
                'waktu_perubahan' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Sub-sub Elemen berhasil dihapus secara permanen!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus permanen: Kriteria masih terhubung dengan data audit.');
        }
    }
}
