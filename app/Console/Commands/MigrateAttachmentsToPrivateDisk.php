<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateAttachmentsToPrivateDisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachments:migrate-to-private 
                            {--dry-run : Pratinjau berkas yang akan disalin/dipindahkan tanpa melakukan modifikasi}
                            {--delete-source : Hapus berkas dari disk publik setelah berhasil disalin ke disk privat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasikan berkas lampiran audit dan bukti PICA dari disk publik ke disk privat attachments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pemindaian berkas pada disk public (folder lampiran/ dan bukti_pica/)...');

        $isDryRun = (bool) $this->option('dry-run');
        $deleteSource = (bool) $this->option('delete-source');

        $publicDisk = Storage::disk('public');
        $privateDisk = Storage::disk('attachments');

        $lampiranFiles = $publicDisk->allFiles('lampiran');
        $picaFiles = $publicDisk->allFiles('bukti_pica');

        $allFiles = array_values(array_filter(array_merge($lampiranFiles, $picaFiles), function ($file) {
            return !str_ends_with($file, '.gitignore');
        }));

        $totalCount = count($allFiles);

        if ($totalCount === 0) {
            $this->info('Tidak ditemukan berkas lampiran atau bukti PICA di disk publik.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$totalCount} berkas di disk publik.");

        if ($isDryRun) {
            $this->warn('[DRY-RUN AKTIF] Berkas-berkas berikut akan disalin ke disk privat (tidak ada perubahan aktual):');
            $rows = [];
            foreach ($allFiles as $idx => $file) {
                $sizeKb = round($publicDisk->size($file) / 1024, 2);
                $rows[] = [$idx + 1, $file, "attachments/{$file}", "{$sizeKb} KB"];
            }
            $this->table(['No', 'Sumber (Public)', 'Tujuan (Private)', 'Ukuran'], $rows);
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($totalCount);
        $bar->start();

        $migrated = 0;
        $failed = 0;

        foreach ($allFiles as $file) {
            try {
                $contents = $publicDisk->get($file);
                if ($contents !== null) {
                    $privateDisk->put($file, $contents);

                    if ($deleteSource) {
                        $publicDisk->delete($file);
                    }
                    $migrated++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Migrasi selesai!");
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Berkas Terdeteksi', $totalCount],
                ['Berhasil Dimigrasikan', $migrated],
                ['Gagal Dimigrasikan', $failed],
                ['Sumber Dihapus dari Public', $deleteSource ? 'Ya' : 'Tidak (Disimpan)'],
            ]
        );

        return Command::SUCCESS;
    }
}
