<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\SubElemen;
use Illuminate\Http\Request;

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
            'kode_kriteria' => 'required|string|max:50',
            'deskripsi' => 'required|string',
            'nilai_maksimal' => 'required|numeric|min:0.01|max:100',
            'persyaratan_dokumen' => 'nullable|string',
            'pedoman_nilai_0' => 'nullable|string',
            'pedoman_nilai_1' => 'nullable|string',
            'pedoman_nilai_2' => 'nullable|string',
            'pedoman_nilai_3' => 'nullable|string',
            'pedoman_nilai_4' => 'nullable|string',
            'dependency_id' => 'nullable|exists:kriterias,id',
            'dependency_note' => 'nullable|string',
        ]);

        Kriteria::create($request->all());

        return redirect()->route('admin.kriterias.index')
            ->with('success', 'Kriteria baru berhasil ditambahkan!');
    }

    /**
     * Update the specified criteria in storage.
     */
    public function update(Request $request, $id)
    {
        $kriteria = Kriteria::findOrFail($id);

        $request->validate([
            'sub_elemen_id' => 'required|exists:sub_elemens,id',
            'kode_kriteria' => 'required|string|max:50',
            'deskripsi' => 'required|string',
            'nilai_maksimal' => 'required|numeric|min:0.01|max:100',
            'persyaratan_dokumen' => 'nullable|string',
            'pedoman_nilai_0' => 'nullable|string',
            'pedoman_nilai_1' => 'nullable|string',
            'pedoman_nilai_2' => 'nullable|string',
            'pedoman_nilai_3' => 'nullable|string',
            'pedoman_nilai_4' => 'nullable|string',
            'dependency_id' => 'nullable|exists:kriterias,id',
            'dependency_note' => 'nullable|string',
        ]);

        if ($request->filled('dependency_id')) {
            $newDepId = (int) $request->dependency_id;
            if ($this->wouldCreateCycle($kriteria->id, $newDepId)) {
                return back()->withInput()->withErrors([
                    'dependency_id' => 'Kriteria prasyarat yang dipilih akan membentuk siklus dependensi (A bergantung ke B, B bergantung ke A). Pilih kriteria lain.'
                ]);
            }
        }

        $kriteria->update($request->all());

        return redirect()->route('admin.kriterias.index')
            ->with('success', 'Data kriteria berhasil diperbarui!');
    }

    /**
     * Remove the specified criteria from storage.
     */
    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $kriteria->delete();

        return redirect()->route('admin.kriterias.index')
            ->with('success', 'Kriteria berhasil dinonaktifkan.');
    }
}
