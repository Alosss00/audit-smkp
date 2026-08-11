<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Elemen;
use App\Models\SubElemen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubElemenController extends Controller
{
    /**
     * Display a listing of sub-elemens.
     */
    public function index()
    {
        $subElemens = SubElemen::with('elemen')->withCount('kriterias')->orderBy('kode_sub')->get();
        $elemens = Elemen::orderBy('kode_elemen')->get();

        return view('admin.sub_elemens.index', compact('subElemens', 'elemens'));
    }

    /**
     * Store a newly created sub-elemen in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'elemen_id' => 'required|exists:elemens,id',
            'kode_sub' => [
                'required',
                'string',
                Rule::unique('sub_elemens')->where(function ($query) use ($request) {
                    return $query->where('elemen_id', $request->elemen_id);
                }),
            ],
            'nama_sub' => 'required|string|max:255',
        ], [
            'kode_sub.unique' => 'Kode Sub-Elemen sudah digunakan untuk elemen ini.',
        ]);

        SubElemen::create($request->only(['elemen_id', 'kode_sub', 'nama_sub']));

        return redirect()->route('admin.sub-elemens.index')
            ->with('success', 'Master Sub-Elemen baru berhasil ditambahkan!');
    }

    /**
     * Update the specified sub-elemen in storage.
     */
    public function update(Request $request, $id)
    {
        $subElemen = SubElemen::findOrFail($id);

        $request->validate([
            'elemen_id' => 'required|exists:elemens,id',
            'kode_sub' => [
                'required',
                'string',
                Rule::unique('sub_elemens')->where(function ($query) use ($request, $subElemen) {
                    return $query->where('elemen_id', $request->elemen_id);
                })->ignore($subElemen->id),
            ],
            'nama_sub' => 'required|string|max:255',
        ]);

        $subElemen->update($request->only(['elemen_id', 'kode_sub', 'nama_sub']));

        return redirect()->route('admin.sub-elemens.index')
            ->with('success', 'Master Sub-Elemen berhasil diperbarui!');
    }

    /**
     * Remove the specified sub-elemen from storage (Soft Delete).
     */
    public function destroy($id)
    {
        $subElemen = SubElemen::findOrFail($id);
        $subElemen->delete();

        return redirect()->route('admin.sub-elemens.index')
            ->with('success', 'Master Sub-Elemen berhasil dinonaktifkan (Soft Delete).');
    }
}
