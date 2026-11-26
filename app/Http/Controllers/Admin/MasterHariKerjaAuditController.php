<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MasterHariKerjaAudit;
use App\Services\HariKerjaAuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MasterHariKerjaAuditController extends Controller
{
    public const EXPECTED_CSV_HEADERS = [
        'jumlah_min',
        'jumlah_max',
        'hari_tinggi',
        'hari_menengah',
        'hari_rendah',
        'sumber',
    ];

    public function __construct(protected HariKerjaAuditService $service)
    {
    }

    public function index(): View
    {
        $items = MasterHariKerjaAudit::orderBy('jumlah_min')->get();
        $trashedItems = MasterHariKerjaAudit::onlyTrashed()->latest('deleted_at')->get();

        return view('admin.master-hari-kerja.index', compact('items', 'trashedItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRow($request);

        $overlap = $this->service->findOverlappingRange(
            (int) $validated['jumlah_min'],
            $validated['jumlah_max'] !== null ? (int) $validated['jumlah_max'] : null
        );
        if ($overlap) {
            return back()
                ->withInput()
                ->with('error', "Rentang pekerja bertumpuk dengan baris acuan #{$overlap->id} ({$overlap->rentang_label}).");
        }

        $item = MasterHariKerjaAudit::create([
            'jumlah_min'    => (int) $validated['jumlah_min'],
            'jumlah_max'    => $validated['jumlah_max'] !== null ? (int) $validated['jumlah_max'] : null,
            'hari_tinggi'   => (float) $validated['hari_tinggi'],
            'hari_menengah' => (float) $validated['hari_menengah'],
            'hari_rendah'   => (float) $validated['hari_rendah'],
            'is_verified'   => $request->boolean('is_verified'),
            'sumber'        => $validated['sumber'] ?? null,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Hari Kerja Audit',
            'tindakan'        => "Menambahkan baris acuan hari kerja #{$item->id} ({$item->rentang_label})",
            'data_lama'       => null,
            'data_baru'       => $item->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-hari-kerja.index')
            ->with('success', "Baris acuan hari kerja ({$item->rentang_label}) berhasil ditambahkan.");
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $item = MasterHariKerjaAudit::findOrFail($id);
        $oldData = $item->toArray();
        $validated = $this->validateRow($request);

        $overlap = $this->service->findOverlappingRange(
            (int) $validated['jumlah_min'],
            $validated['jumlah_max'] !== null ? (int) $validated['jumlah_max'] : null,
            $item->id
        );
        if ($overlap) {
            return back()
                ->withInput()
                ->with('error', "Rentang pekerja bertumpuk dengan baris acuan #{$overlap->id} ({$overlap->rentang_label}).");
        }

        $item->update([
            'jumlah_min'    => (int) $validated['jumlah_min'],
            'jumlah_max'    => $validated['jumlah_max'] !== null ? (int) $validated['jumlah_max'] : null,
            'hari_tinggi'   => (float) $validated['hari_tinggi'],
            'hari_menengah' => (float) $validated['hari_menengah'],
            'hari_rendah'   => (float) $validated['hari_rendah'],
            'is_verified'   => $request->boolean('is_verified'),
            'sumber'        => $validated['sumber'] ?? null,
        ]);

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Hari Kerja Audit',
            'tindakan'        => "Memperbarui baris acuan hari kerja #{$item->id} ({$item->rentang_label})",
            'data_lama'       => $oldData,
            'data_baru'       => $item->fresh()->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-hari-kerja.index')
            ->with('success', "Baris acuan hari kerja ({$item->rentang_label}) berhasil diperbarui.");
    }

    public function toggleVerify(int|string $id): RedirectResponse
    {
        $item = MasterHariKerjaAudit::findOrFail($id);
        $old = $item->is_verified;
        $item->update(['is_verified' => ! $old]);

        $label = $item->is_verified ? 'Terverifikasi' : 'Belum Terverifikasi';
        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Hari Kerja Audit',
            'tindakan'        => "Mengubah status verifikasi baris #{$item->id} ({$item->rentang_label}) menjadi {$label}",
            'data_lama'       => ['is_verified' => $old],
            'data_baru'       => ['is_verified' => $item->is_verified],
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-hari-kerja.index')
            ->with('success', "Status verifikasi baris ({$item->rentang_label}) diubah menjadi {$label}.");
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $item = MasterHariKerjaAudit::findOrFail($id);
        $oldData = $item->toArray();
        $label = $item->rentang_label;
        $item->delete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Hari Kerja Audit',
            'tindakan'        => "Menghapus (soft delete) baris acuan hari kerja #{$oldData['id']} ({$label})",
            'data_lama'       => $oldData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-hari-kerja.index')
            ->with('success', "Baris acuan ({$label}) berhasil dipindahkan ke tempat sampah.");
    }

    public function restore(int|string $id): RedirectResponse
    {
        $item = MasterHariKerjaAudit::onlyTrashed()->findOrFail($id);

        $overlap = $this->service->findOverlappingRange($item->jumlah_min, $item->jumlah_max);
        if ($overlap) {
            return redirect()->route('admin.master-hari-kerja.index')
                ->with('error', "Gagal memulihkan baris #{$item->id} karena rentangnya bertumpuk dengan baris aktif #{$overlap->id} ({$overlap->rentang_label}).");
        }

        $item->restore();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Hari Kerja Audit',
            'tindakan'        => "Memulihkan baris acuan hari kerja #{$item->id} ({$item->rentang_label})",
            'data_lama'       => null,
            'data_baru'       => $item->toArray(),
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-hari-kerja.index')
            ->with('success', "Baris acuan ({$item->rentang_label}) berhasil dipulihkan.");
    }

    public function forceDelete(int|string $id): RedirectResponse
    {
        $item = MasterHariKerjaAudit::onlyTrashed()->findOrFail($id);
        $oldData = $item->toArray();
        $item->forceDelete();

        AuditLog::create([
            'user_id'         => auth()->id(),
            'modul'           => 'Master Hari Kerja Audit',
            'tindakan'        => "Menghapus permanen baris acuan hari kerja #{$oldData['id']}",
            'data_lama'       => $oldData,
            'data_baru'       => null,
            'waktu_perubahan' => now(),
        ]);

        return redirect()->route('admin.master-hari-kerja.index')
            ->with('success', 'Baris acuan berhasil dihapus permanen.');
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $path = database_path('data/hari_kerja_audit.template.csv');
        abort_unless(file_exists($path), 404, 'File template CSV tidak ditemukan.');

        return response()->download($path, 'hari_kerja_audit.template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importPreview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:1024',
        ], [
            'csv_file.required' => 'File CSV wajib dipilih.',
            'csv_file.file'     => 'Unggahan harus berupa berkas yang valid.',
            'csv_file.mimes'    => 'Format berkas harus CSV atau TXT.',
            'csv_file.max'      => 'Ukuran berkas CSV maksimal 1 MB.',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', 'Gagal membaca berkas CSV yang diunggah.');
        }

        $headerRow = fgetcsv($handle);
        if (! is_array($headerRow)) {
            fclose($handle);
            return back()->with('error', 'Berkas CSV kosong atau tidak memiliki baris header.');
        }

        // Strip UTF-8 BOM if present
        $headerRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headerRow[0]);
        $normalizedHeaders = array_map(fn ($h) => strtolower(trim((string) $h)), $headerRow);

        if ($normalizedHeaders !== self::EXPECTED_CSV_HEADERS) {
            fclose($handle);
            return back()->with(
                'error',
                'Header CSV tidak sesuai. Harus persis: ' . implode(',', self::EXPECTED_CSV_HEADERS)
            );
        }

        $parsedRows = [];
        $validRows = [];
        $hasErrors = false;
        $lineNum = 1;

        while (($raw = fgetcsv($handle)) !== false) {
            $lineNum++;
            // Skip completely blank lines
            if ($raw === [null] || count(array_filter($raw, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $raw = array_pad($raw, 6, '');
            $minStr = trim((string) $raw[0]);
            $maxStr = trim((string) $raw[1]);
            $tinggiStr = str_replace(',', '.', trim((string) $raw[2]));
            $menengahStr = str_replace(',', '.', trim((string) $raw[3]));
            $rendahStr = str_replace(',', '.', trim((string) $raw[4]));
            $sumber = trim((string) $raw[5]);

            $rowErrors = [];
            if ($minStr === '' || ! ctype_digit($minStr) || (int) $minStr < 1) {
                $rowErrors[] = 'jumlah_min harus bilangan bulat minimal 1.';
            }
            if ($maxStr !== '' && (! ctype_digit($maxStr) || (int) $maxStr < 1)) {
                $rowErrors[] = 'jumlah_max harus kosong atau bilangan bulat minimal 1.';
            }

            $minVal = ctype_digit($minStr) ? (int) $minStr : null;
            $maxVal = ($maxStr !== '' && ctype_digit($maxStr)) ? (int) $maxStr : null;

            if ($minVal !== null && $maxVal !== null && $minVal > $maxVal) {
                $rowErrors[] = 'jumlah_min tidak boleh lebih besar dari jumlah_max.';
            }

            foreach (['hari_tinggi' => $tinggiStr, 'hari_menengah' => $menengahStr, 'hari_rendah' => $rendahStr] as $field => $val) {
                if ($val === '' || ! is_numeric($val) || (float) $val <= 0) {
                    $rowErrors[] = "{$field} harus angka positif (> 0).";
                }
            }

            if (mb_strlen($sumber) > 255) {
                $rowErrors[] = 'sumber maksimal 255 karakter.';
            }

            // Check overlap with existing active DB rows
            if (empty($rowErrors) && $minVal !== null) {
                $dbOverlap = $this->service->findOverlappingRange($minVal, $maxVal);
                if ($dbOverlap) {
                    $rowErrors[] = "Bertumpuk dengan baris acuan DB #{$dbOverlap->id} ({$dbOverlap->rentang_label}).";
                }

                // Check overlap with earlier valid rows in the same CSV
                foreach ($validRows as $prev) {
                    if ($this->service->rangesOverlap($minVal, $maxVal, $prev['jumlah_min'], $prev['jumlah_max'])) {
                        $rowErrors[] = "Bertumpuk dengan baris CSV nomor {$prev['line']}.";
                        break;
                    }
                }
            }

            $rowData = [
                'line'          => $lineNum,
                'jumlah_min'    => $minVal,
                'jumlah_max'    => $maxVal,
                'hari_tinggi'   => is_numeric($tinggiStr) ? round((float) $tinggiStr, 1) : $tinggiStr,
                'hari_menengah' => is_numeric($menengahStr) ? round((float) $menengahStr, 1) : $menengahStr,
                'hari_rendah'   => is_numeric($rendahStr) ? round((float) $rendahStr, 1) : $rendahStr,
                'sumber'        => $sumber !== '' ? $sumber : null,
                'errors'        => $rowErrors,
            ];

            $parsedRows[] = $rowData;
            if (! empty($rowErrors)) {
                $hasErrors = true;
            } else {
                $validRows[] = $rowData;
            }
        }
        fclose($handle);

        if (empty($parsedRows)) {
            return back()->with('error', 'Tidak ada baris data yang ditemukan di dalam berkas CSV.');
        }

        if (! $hasErrors) {
            $request->session()->put('hari_kerja_csv_preview', $validRows);
        } else {
            $request->session()->forget('hari_kerja_csv_preview');
        }

        return view('admin.master-hari-kerja.preview-import', [
            'parsedRows' => $parsedRows,
            'validCount' => count($validRows),
            'hasErrors'  => $hasErrors,
        ]);
    }

    public function importConfirm(Request $request): RedirectResponse
    {
        $rows = $request->session()->pull('hari_kerja_csv_preview');
        if (! is_array($rows) || empty($rows)) {
            return redirect()->route('admin.master-hari-kerja.index')
                ->with('error', 'Data pratinjau impor tidak ditemukan atau sudah kedaluwarsa. Silakan unggah ulang CSV.');
        }

        // Re-verify no overlap occurred since preview
        foreach ($rows as $row) {
            $overlap = $this->service->findOverlappingRange((int) $row['jumlah_min'], $row['jumlah_max'] !== null ? (int) $row['jumlah_max'] : null);
            if ($overlap) {
                return redirect()->route('admin.master-hari-kerja.index')
                    ->with('error', "Impor dibatalkan: baris CSV #{$row['line']} bertumpuk dengan baris DB #{$overlap->id} ({$overlap->rentang_label}).");
            }
        }

        DB::beginTransaction();
        try {
            $createdCount = 0;
            foreach ($rows as $row) {
                MasterHariKerjaAudit::create([
                    'jumlah_min'    => (int) $row['jumlah_min'],
                    'jumlah_max'    => $row['jumlah_max'] !== null ? (int) $row['jumlah_max'] : null,
                    'hari_tinggi'   => (float) $row['hari_tinggi'],
                    'hari_menengah' => (float) $row['hari_menengah'],
                    'hari_rendah'   => (float) $row['hari_rendah'],
                    'is_verified'   => false,
                    'sumber'        => $row['sumber'] ?? null,
                ]);
                $createdCount++;
            }

            AuditLog::create([
                'user_id'         => auth()->id(),
                'modul'           => 'Master Hari Kerja Audit',
                'tindakan'        => "Mengimpor {$createdCount} baris acuan hari kerja dari CSV (status: belum terverifikasi)",
                'data_lama'       => null,
                'data_baru'       => ['imported_count' => $createdCount],
                'waktu_perubahan' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.master-hari-kerja.index')
                ->with('success', "Berhasil mengimpor {$createdCount} baris acuan hari kerja (status: Belum Terverifikasi).");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.master-hari-kerja.index')
                ->with('error', 'Gagal mengimpor CSV: ' . $e->getMessage());
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateRow(Request $request): array
    {
        return $request->validate([
            'jumlah_min'    => 'required|integer|min:1',
            'jumlah_max'    => 'nullable|integer|gte:jumlah_min',
            'hari_tinggi'   => 'required|numeric|gt:0|max:9999.9',
            'hari_menengah' => 'required|numeric|gt:0|max:9999.9',
            'hari_rendah'   => 'required|numeric|gt:0|max:9999.9',
            'sumber'        => 'nullable|string|max:255',
        ], [
            'jumlah_min.required'    => 'Jumlah pekerja minimum wajib diisi.',
            'jumlah_min.integer'     => 'Jumlah pekerja minimum harus bilangan bulat.',
            'jumlah_min.min'         => 'Jumlah pekerja minimum minimal 1.',
            'jumlah_max.integer'     => 'Jumlah pekerja maksimum harus bilangan bulat.',
            'jumlah_max.gte'         => 'Jumlah pekerja maksimum harus lebih besar atau sama dengan minimum.',
            'hari_tinggi.required'   => 'Hari kerja risiko Tinggi wajib diisi.',
            'hari_tinggi.gt'         => 'Hari kerja risiko Tinggi harus lebih besar dari 0.',
            'hari_menengah.required' => 'Hari kerja risiko Menengah wajib diisi.',
            'hari_menengah.gt'       => 'Hari kerja risiko Menengah harus lebih besar dari 0.',
            'hari_rendah.required'   => 'Hari kerja risiko Rendah wajib diisi.',
            'hari_rendah.gt'         => 'Hari kerja risiko Rendah harus lebih besar dari 0.',
            'sumber.max'             => 'Sumber maksimal 255 karakter.',
        ]);
    }
}
