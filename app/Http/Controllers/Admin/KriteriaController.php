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
     * Display a listing of criteria.
     */
    public function index()
    {
        $kriterias = Kriteria::with(['subElemen.elemen', 'dependency'])->latest()->get();
        $subElemens = SubElemen::with('elemen')->orderBy('kode_sub')->get();

        return view('admin.kriterias.index', compact('kriterias', 'subElemens'));
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
     * Store a newly created criteria in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_elemen_id' => 'required|exists:sub_elemens,id',
            'kode_kriteria' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kriterias')->where(function ($query) use ($request) {
                    return $query->where('sub_elemen_id', $request->sub_elemen_id)
                                 ->whereNull('deleted_at');
                }),
            ],
            'deskripsi'           => 'required|string',
            'nilai_maksimal'      => 'required|numeric|min:0|max:100',
            'persyaratan_dokumen' => 'nullable|string',
            'pedoman_nilai_0'     => 'nullable|string',
            'pedoman_nilai_1'     => 'nullable|string',
            'pedoman_nilai_2'     => 'nullable|string',
            'pedoman_nilai_3'     => 'nullable|string',
            'pedoman_nilai_4'     => 'nullable|string',
            'dependency_id'       => 'nullable|exists:kriterias,id',
            'dependency_note'     => 'nullable|string',
        ], [
            'sub_elemen_id.required' => 'Induk Sub-Elemen wajib dipilih.',
            'kode_kriteria.required' => 'Kode Sub-sub Elemen wajib diisi.',
            'kode_kriteria.unique'   => 'Kode Sub-sub Elemen (Kriteria) sudah digunakan untuk Sub-Elemen ini.',
            'deskripsi.required'     => 'Deskripsi pertanyaan Sub-sub Elemen wajib diisi.',
            'nilai_maksimal.required'=> 'Nilai maksimal wajib diisi.',
        ]);

        $trashedKriteria = Kriteria::onlyTrashed()
            ->where('sub_elemen_id', $request->sub_elemen_id)
            ->where('kode_kriteria', $request->kode_kriteria)
            ->first();

        if ($trashedKriteria) {
            $data = $request->all();
            $data['is_na'] = $request->has('is_na') ? true : false;
            $trashedKriteria->restore();
            $trashedKriteria->update($data);

            if ($trashedKriteria->subElemen) {
                $trashedKriteria->subElemen->update([
                    'nilai_maksimal' => (float) $trashedKriteria->subElemen->kriterias()->sum('nilai_maksimal')
                ]);
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Master Kriteria',
                'tindakan'        => "Diaktifkan kembali Sub-sub Elemen (Kriteria): {$trashedKriteria->kode_kriteria}",
                'data_lama'       => null,
                'data_baru'       => $trashedKriteria->toArray(),
                'waktu_perubahan' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Sub-sub Elemen (Kriteria) berhasil diaktifkan kembali!');
        }

        $data = $request->all();
        $data['is_na'] = $request->has('is_na') ? true : false;
        $kriteria = Kriteria::create($data);

        // Sync parent SubElemen total score to match sum of child kriterias
        if ($kriteria->subElemen) {
            $kriteria->subElemen->update([
                'nilai_maksimal' => (float) $kriteria->subElemen->kriterias()->sum('nilai_maksimal')
            ]);
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Kriteria',
            'tindakan'        => "Membuat Sub-sub Elemen (Kriteria) baru: {$kriteria->kode_kriteria}",
            'data_lama'       => null,
            'data_baru'       => $kriteria->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Sub-sub Elemen (Kriteria) baru berhasil ditambahkan!');
    }

    /**
     * Update the specified criteria in storage.
     */
    public function update(Request $request, $id)
    {
        $kriteria = Kriteria::findOrFail($id);

        $request->validate([
            'sub_elemen_id' => 'required|exists:sub_elemens,id',
            'kode_kriteria' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kriterias')->where(function ($query) use ($request) {
                    return $query->where('sub_elemen_id', $request->sub_elemen_id)
                                 ->whereNull('deleted_at');
                })->ignore($kriteria->id),
            ],
            'deskripsi'           => 'required|string',
            'nilai_maksimal'      => 'required|numeric|min:0|max:100',
            'persyaratan_dokumen' => 'nullable|string',
            'pedoman_nilai_0'     => 'nullable|string',
            'pedoman_nilai_1'     => 'nullable|string',
            'pedoman_nilai_2'     => 'nullable|string',
            'pedoman_nilai_3'     => 'nullable|string',
            'pedoman_nilai_4'     => 'nullable|string',
            'dependency_id'       => 'nullable|exists:kriterias,id',
            'dependency_note'     => 'nullable|string',
        ], [
            'kode_kriteria.unique' => 'Kode Sub-sub Elemen (Kriteria) sudah digunakan untuk Sub-Elemen ini.',
        ]);

        if ($request->filled('dependency_id')) {
            $newDepId = (int) $request->dependency_id;
            if ($this->wouldCreateCycle($kriteria->id, $newDepId)) {
                return back()->withInput()->withErrors([
                    'dependency_id' => 'Kriteria prasyarat yang dipilih akan membentuk siklus dependensi (A bergantung ke B, B bergantung ke A). Pilih kriteria lain.'
                ]);
            }
        }

        $oldSubElemenId = $kriteria->sub_elemen_id;
        $originalData = $kriteria->toArray();
        
        $data = $request->all();
        $data['is_na'] = $request->has('is_na') ? true : false;
        $kriteria->update($data);

        // Sync parent SubElemen total score to match sum of child kriterias
        if ($kriteria->subElemen) {
            $kriteria->subElemen->update([
                'nilai_maksimal' => (float) $kriteria->subElemen->kriterias()->sum('nilai_maksimal')
            ]);
        }
        if ($oldSubElemenId != $kriteria->sub_elemen_id) {
            $oldSub = SubElemen::find($oldSubElemenId);
            if ($oldSub) {
                $oldSub->update([
                    'nilai_maksimal' => (float) $oldSub->kriterias()->sum('nilai_maksimal')
                ]);
            }
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Kriteria',
            'tindakan'        => "Mengubah Sub-sub Elemen (Kriteria): {$kriteria->kode_kriteria}",
            'data_lama'       => $originalData,
            'data_baru'       => $kriteria->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Data Sub-sub Elemen berhasil diperbarui!');
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
            $subElemen->update([
                'nilai_maksimal' => (float) $subElemen->kriterias()->sum('nilai_maksimal')
            ]);
        }

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Kriteria',
            'tindakan'        => "Menonaktifkan Sub-sub Elemen (Kriteria): {$kriteriaData['kode_kriteria']}",
            'data_lama'       => $kriteriaData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Sub-sub Elemen berhasil dinonaktifkan.');
    }
}
