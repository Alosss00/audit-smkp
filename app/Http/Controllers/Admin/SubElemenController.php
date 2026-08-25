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
        $elemens = Elemen::with(['subElemens.kriterias'])->orderBy('kode_elemen')->get();
        $subElemens = SubElemen::with(['elemen', 'kriterias'])->withCount('kriterias')->orderBy('kode_sub')->get();
        $allKriterias = \App\Models\Kriteria::orderBy('kode_kriteria')->get();

        return view('admin.sub_elemens.index', compact('elemens', 'subElemens', 'allKriterias'));
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
                    return $query->where('elemen_id', $request->elemen_id)
                                 ->whereNull('deleted_at');
                }),
            ],
            'nama_sub' => 'required|string|max:255',
            'nilai_maksimal' => 'nullable|numeric|min:0|max:1000',
        ], [
            'kode_sub.unique' => 'Kode Sub-Elemen sudah digunakan untuk elemen ini.',
        ]);

        $elemen = Elemen::findOrFail($request->elemen_id);
        if ($elemen->total_nilai_sub_elemen > 0) {
            $inputNilai = (float) ($request->nilai_maksimal ?? 0);
            $existingSum = (float) SubElemen::where('elemen_id', $elemen->id)->sum('nilai_maksimal');
            $newTotal = $existingSum + $inputNilai;

            if ($newTotal > $elemen->total_nilai_sub_elemen) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'nilai_maksimal' => "Total akumulasi nilai sub-elemen (" . number_format($newTotal, 2) . ") melebihi batas Total Nilai Sub-Elemen (" . number_format($elemen->total_nilai_sub_elemen, 2) . ") pada Elemen {$elemen->kode_elemen}."
                    ]);
            }
        }

        $trashed = SubElemen::onlyTrashed()
            ->where('elemen_id', $request->elemen_id)
            ->where('kode_sub', $request->kode_sub)
            ->first();

        if ($trashed) {
            $data = $request->only(['elemen_id', 'kode_sub', 'nama_sub', 'nilai_maksimal']);
            $data['is_na'] = $request->has('is_na') ? true : false;
            $trashed->restore();
            $trashed->update($data);

            return redirect()->route('admin.sub-elemens.index')
                ->with('success', 'Master Sub-Elemen berhasil diaktifkan kembali dan diperbarui!');
        }

        $data = $request->only(['elemen_id', 'kode_sub', 'nama_sub', 'nilai_maksimal']);
        $data['is_na'] = $request->has('is_na') ? true : false;
        SubElemen::create($data);

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
                    return $query->where('elemen_id', $request->elemen_id)
                                 ->whereNull('deleted_at');
                })->ignore($subElemen->id),
            ],
            'nama_sub' => 'required|string|max:255',
            'nilai_maksimal' => 'nullable|numeric|min:0|max:1000',
        ]);

        $elemen = Elemen::findOrFail($request->elemen_id);
        if ($elemen->total_nilai_sub_elemen > 0) {
            $inputNilai = (float) ($request->nilai_maksimal ?? 0);
            $existingSum = (float) SubElemen::where('elemen_id', $elemen->id)
                ->where('id', '!=', $subElemen->id)
                ->sum('nilai_maksimal');
            $newTotal = $existingSum + $inputNilai;

            if ($newTotal > $elemen->total_nilai_sub_elemen) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'nilai_maksimal' => "Total akumulasi nilai sub-elemen (" . number_format($newTotal, 2) . ") melebihi batas Total Nilai Sub-Elemen (" . number_format($elemen->total_nilai_sub_elemen, 2) . ") pada Elemen {$elemen->kode_elemen}."
                    ]);
            }
        }

        $data = $request->only(['elemen_id', 'kode_sub', 'nama_sub', 'nilai_maksimal']);
        $data['is_na'] = $request->has('is_na') ? true : false;
        $subElemen->update($data);
        $subElemen->syncNilaiMaksimalFromKriterias();

        return redirect()->route('admin.sub-elemens.index')
            ->with('success', 'Master Sub-Elemen berhasil diperbarui!');
    }

    /**
     * Toggle N/A (Not Applicable) status for a Sub-Elemen and all its child Kriterias.
     */
    public function toggleNa($id)
    {
        $subElemen = SubElemen::findOrFail($id);
        $newNaState = !$subElemen->is_na;

        $subElemen->update(['is_na' => $newNaState]);

        // Cascading update to all child kriterias under this sub-elemen
        $subElemen->kriterias()->update(['is_na' => $newNaState]);

        $statusText = $newNaState ? 'di-set N/A (Not Applicable)' : 'diaktifkan kembali (Aktif)';

        return redirect()->back()
            ->with('success', "Sub-Elemen {$subElemen->kode_sub} beserta seluruh Sub-sub Elemen di bawahnya berhasil {$statusText}!");
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
