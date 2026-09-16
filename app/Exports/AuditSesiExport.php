<?php

namespace App\Exports;

use App\Models\AuditSesi;
use App\Models\Elemen;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditSesiExport implements FromView, WithTitle, ShouldAutoSize, WithEvents
{
    protected $sesi;

    public function __construct(AuditSesi $sesi)
    {
        $this->sesi = $sesi;
    }

    /**
     * Populate official template TT-MGT-FRS-026B with session scores and return stream download.
     */
    public static function downloadTemplateWithScores(AuditSesi $sesi, string $fileName): StreamedResponse
    {
        $templatePath = base_path('TT-MGT-FRS-026B Formulir Kriteria Audit SMKP_Rev.xlsx');

        if (!file_exists($templatePath)) {
            // Check in subdirectory if needed
            $subPath = base_path('TT-MGT-FRS-026B Formulir Kriteria Audit SMKP_Rev/TT-MGT-FRS-026B Formulir Kriteria Audit SMKP_Rev.xlsx');
            if (file_exists($subPath)) {
                $templatePath = $subPath;
            }
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getSheetByName('CHECKLIST') ?? $spreadsheet->getActiveSheet();

        // Header Metadata
        $perusahaanNama = $sesi->perusahaan->nama_perusahaan ?? $sesi->area_audit;
        $areaNama = $sesi->departemen->nama_departemen ?? $sesi->area_audit;
        $auditorNama = $sesi->user->name ?? 'Auditor Internal';
        $periode = $sesi->periode ?? ($sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y') : date('Y'));

        $sheet->setCellValue('B3', 'PERUSAHAAN: ' . strtoupper($perusahaanNama));
        $sheet->setCellValue('B4', "AREA AUDIT: {$areaNama} | AUDITOR: {$auditorNama} | PERIODE: {$periode}");

        // Map audit details by kode_kriteria
        $detailsByKode = [];
        foreach ($sesi->auditDetails as $d) {
            if ($d->kriteria) {
                $kode = trim($d->kriteria->kode_kriteria);
                $detailsByKode[$kode] = $d;
            }
        }

        // 1. Remove all conditional formatting rules from the duplicate to prevent red error/blank highlights
        foreach (array_keys($sheet->getConditionalStylesCollection()) as $range) {
            $sheet->removeConditionalStyles($range);
        }

        // 2. Loop rows 7 to 133 to fill scores and clean all unused cells
        for ($row = 7; $row <= 133; $row++) {
            $colC = trim((string)$sheet->getCell("C{$row}")->getValue());
            $colD = trim((string)$sheet->getCell("D{$row}")->getValue());
            $colI = (string)$sheet->getCell("I{$row}")->getValue();

            // A. Standalone Sub-Elemen (e.g. C=I.1, I=4 without SUM formula)
            if (!empty($colC) && preg_match('/^[I|V|X]+\.\d+$/', $colC) && strpos($colI, '=SUM') === false) {
                // Kolom K is used
                if (isset($detailsByKode[$colC])) {
                    $d = $detailsByKode[$colC];
                    if ($d->is_na) {
                        $sheet->setCellValue("K{$row}", 'N/A');
                    } else {
                        $sheet->setCellValue("K{$row}", (float)$d->nilai);
                    }
                    if (!empty($d->catatan)) {
                        $sheet->setCellValue("O{$row}", $d->catatan);
                    } else {
                        $sheet->setCellValue("O{$row}", null);
                    }
                } else {
                    $sheet->setCellValue("K{$row}", null);
                    $sheet->setCellValue("O{$row}", null);
                }

                // Kolom L is unused in this row -> empty and clear any fill
                $sheet->setCellValue("L{$row}", null);
                $sheet->getStyle("L{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);
            }
            // B. Parent Sub-Elemen with SUM formula (e.g. C=II.2, I==SUM(J18:J22))
            elseif (!empty($colC) && preg_match('/^[I|V|X]+\.\d+$/', $colC) && strpos($colI, '=SUM') !== false) {
                // Kolom K has the SUM formula (e.g. =SUM(L18:L22)), leave formula intact
                // Kolom L is unused in this parent row -> empty and clear fill
                $sheet->setCellValue("L{$row}", null);
                $sheet->getStyle("L{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);
                $sheet->setCellValue("O{$row}", null);
            }
            // C. Sub-sub Kriteria (e.g. D=II.2.1)
            elseif (!empty($colD) && preg_match('/^[I|V|X]+\.\d+\.\d+$/', $colD)) {
                // Kolom L is used
                if (isset($detailsByKode[$colD])) {
                    $d = $detailsByKode[$colD];
                    if ($d->is_na) {
                        $sheet->setCellValue("L{$row}", 'N/A');
                    } else {
                        $sheet->setCellValue("L{$row}", (float)$d->nilai);
                    }
                    if (!empty($d->catatan)) {
                        $sheet->setCellValue("O{$row}", $d->catatan);
                    } else {
                        $sheet->setCellValue("O{$row}", null);
                    }
                } else {
                    $sheet->setCellValue("L{$row}", null);
                    $sheet->setCellValue("O{$row}", null);
                }

                // Kolom K is unused in this row -> empty and clear any fill
                $sheet->setCellValue("K{$row}", null);
                $sheet->getStyle("K{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);
            }
            // D. Spacer or other non-criteria rows
            else {
                $colB = trim((string)$sheet->getCell("B{$row}")->getValue());
                $colE = trim((string)$sheet->getCell("E{$row}")->getValue());

                // If not an Element Header (e.g. B=I, C=KEBIJAKAN), clear unused K, L, O
                if (!preg_match('/^[I|V|X]+$/', $colB)) {
                    $sheet->setCellValue("K{$row}", null);
                    $sheet->setCellValue("L{$row}", null);
                    $sheet->setCellValue("O{$row}", null);
                    $sheet->getStyle("K{$row}:O{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);
                }
            }
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(true);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Render fallback export view template.
     */
    public function view(): View
    {
        libxml_use_internal_errors(true);

        $sesi = $this->sesi;
        $elemens = Elemen::with(['subElemens.kriterias'])->orderBy('kode_elemen')->get();
        $details = $sesi->auditDetails()->with('kriteria')->get();
        $rekapElemen = $sesi->getRekapPerElemen();
        $rekapSub = $sesi->getRekapPerSubElemen();
        $skorAkhir = $sesi->skor_akhir ?? $sesi->hitungSkorAkhir();

        return view('exports.audit-sesi-rekap', compact(
            'sesi',
            'elemens',
            'details',
            'rekapElemen',
            'rekapSub',
            'skorAkhir'
        ));
    }

    /**
     * Worksheet tab title.
     */
    public function title(): string
    {
        return 'CHECKLIST';
    }

    /**
     * Configure spreadsheet events and cell alignment/wrapping.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('E:E')->getAlignment()->setWrapText(true);
                $sheet->getStyle('N:N')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
