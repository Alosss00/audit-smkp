<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    /**
     * Display a listing of departemens.
     */
    public function index(Request $request)
    {
        $query = Departemen::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_departemen', 'like', "%{$search}%")
                  ->orWhere('kode_departemen', 'like', "%{$search}%");
            });
        }

        $departemens = $query->orderBy('nama_departemen')->get();

        return view('admin.departemens.index', compact('departemens'));
    }

    /**
     * Store a newly created departemen.
     */
    public function store(Request $request)
    {
        $nama = $request->nama_departemen;
        if (!str_starts_with(strtolower(trim($nama)), 'departemen')) {
            $nama = 'Departemen ' . trim($nama);
        }

        $request->merge(['nama_departemen' => $nama]);

        $request->validate([
            'nama_departemen' => 'required|string|max:255|unique:departemens,nama_departemen',
            'kode_departemen' => 'nullable|string|max:50',
        ], [
            'nama_departemen.unique' => 'Nama departemen ini sudah terdaftar dalam sistem.',
        ]);

        Departemen::create([
            'nama_departemen' => $request->nama_departemen,
            'kode_departemen' => $request->kode_departemen,
            'is_active'       => true,
        ]);

        return redirect()->route('admin.departemens.index')
            ->with('success', "Departemen '{$request->nama_departemen}' berhasil ditambahkan!");
    }

    /**
     * Update the specified departemen.
     */
    public function update(Request $request, $id)
    {
        $departemen = Departemen::findOrFail($id);

        $nama = $request->nama_departemen;
        if (!str_starts_with(strtolower(trim($nama)), 'departemen')) {
            $nama = 'Departemen ' . trim($nama);
        }

        $request->merge(['nama_departemen' => $nama]);

        $request->validate([
            'nama_departemen' => 'required|string|max:255|unique:departemens,nama_departemen,' . $departemen->id,
            'kode_departemen' => 'nullable|string|max:50',
        ]);

        $departemen->update([
            'nama_departemen' => $request->nama_departemen,
            'kode_departemen' => $request->kode_departemen,
            'is_active'       => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.departemens.index')
            ->with('success', "Data departemen '{$departemen->nama_departemen}' berhasil diperbarui!");
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus($id)
    {
        $departemen = Departemen::findOrFail($id);
        $departemen->is_active = !$departemen->is_active;
        $departemen->save();

        $statusText = $departemen->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.departemens.index')
            ->with('success', "Status departemen '{$departemen->nama_departemen}' berhasil {$statusText}.");
    }

    /**
     * Remove the specified departemen.
     */
    public function destroy($id)
    {
        $departemen = Departemen::findOrFail($id);
        $name = $departemen->nama_departemen;
        $departemen->delete();

        return redirect()->route('admin.departemens.index')
            ->with('success', "Departemen '{$name}' berhasil dihapus.");
    }
}
