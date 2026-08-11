<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Elemen;
use Illuminate\Http\Request;

class ElemenController extends Controller
{
    /**
     * Display a listing of elemens (active & trashed).
     */
    public function index()
    {
        $elemens = Elemen::withCount(['subElemens'])->orderBy('kode_elemen')->get();
        $trashedElemens = Elemen::onlyTrashed()->withCount(['subElemens'])->orderBy('kode_elemen')->get();
        
        $totalBobot = (float) Elemen::sum('bobot');

        return view('admin.elemens.index', compact('elemens', 'trashedElemens', 'totalBobot'));
    }

    /**
     * Display detailed hierarchy of an elemen.
     */
    public function show($id)
    {
        $elemen = Elemen::withTrashed()
            ->with(['subElemens.kriterias'])
            ->findOrFail($id);

        return view('admin.elemens.show', compact('elemen'));
    }

    /**
     * Store a newly created elemen in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_elemen' => 'required|string|unique:elemens,kode_elemen',
            'nama_elemen' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:100',
        ], [
            'kode_elemen.required' => 'Kode elemen wajib diisi.',
            'kode_elemen.unique' => 'Kode elemen sudah digunakan.',
            'nama_elemen.required' => 'Nama elemen wajib diisi.',
            'bobot.required' => 'Bobot elemen wajib diisi.',
        ]);

        Elemen::create($request->only(['kode_elemen', 'nama_elemen', 'bobot']));

        return redirect()->route('admin.elemens.index')
            ->with('success', 'Master Elemen baru berhasil ditambahkan!');
    }

    /**
     * Update the specified elemen in storage.
     */
    public function update(Request $request, $id)
    {
        $elemen = Elemen::withTrashed()->findOrFail($id);

        $request->validate([
            'kode_elemen' => 'required|string|unique:elemens,kode_elemen,' . $elemen->id,
            'nama_elemen' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:100',
        ]);

        $elemen->update($request->only(['kode_elemen', 'nama_elemen', 'bobot']));

        return redirect()->route('admin.elemens.index')
            ->with('success', 'Master Elemen berhasil diperbarui!');
    }

    /**
     * Soft delete the specified elemen from storage.
     */
    public function destroy($id)
    {
        $elemen = Elemen::findOrFail($id);
        $elemen->delete();

        return redirect()->route('admin.elemens.index')
            ->with('success', 'Master Elemen berhasil dinonaktifkan (Soft Delete).');
    }

    /**
     * Restore soft-deleted elemen.
     */
    public function restore($id)
    {
        $elemen = Elemen::onlyTrashed()->findOrFail($id);
        $elemen->restore();

        return redirect()->route('admin.elemens.index')
            ->with('success', 'Master Elemen berhasil diaktifkan kembali!');
    }

    /**
     * Permanently delete elemen.
     */
    public function forceDelete($id)
    {
        $elemen = Elemen::onlyTrashed()->findOrFail($id);

        try {
            $elemen->forceDelete();
            return redirect()->route('admin.elemens.index')
                ->with('success', 'Master Elemen berhasil dihapus secara permanen!');
        } catch (\Exception $e) {
            return redirect()->route('admin.elemens.index')
                ->with('error', 'Gagal menghapus permanen: Elemen masih terhubung dengan data audit.');
        }
    }
}
