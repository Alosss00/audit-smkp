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

        return view('admin.perusahaans.index', compact('perusahaans'));
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

        Perusahaan::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'kode_perusahaan' => $request->kode_perusahaan,
            'kategori'        => $request->kategori,
            'is_active'       => true,
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

        return redirect()->route('admin.perusahaans.index')
            ->with('success', "Status perusahaan '{$perusahaan->nama_perusahaan}' berhasil {$statusText}.");
    }

    /**
     * Remove the specified perusahaan.
     */
    public function destroy($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $name = $perusahaan->nama_perusahaan;
        $perusahaan->delete();

        return redirect()->route('admin.perusahaans.index')
            ->with('success', "Perusahaan '{$name}' berhasil dihapus.");
    }
}
