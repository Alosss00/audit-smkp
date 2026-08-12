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

class AuditSesiExport implements FromView, WithTitle, ShouldAutoSize, WithEvents
{
    protected $sesi;

    public function __construct(AuditSesi $sesi)
    {
        $this->sesi = $sesi;
    }

    /**
     * Render export view template.
     */
    public function view(): View
    {
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

                // Enable wrap text for criteria description & finding notes columns
                $sheet->getStyle('E:E')->getAlignment()->setWrapText(true);
                $sheet->getStyle('N:N')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
