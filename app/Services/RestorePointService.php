<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\RestorePoint;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Exception;

class RestorePointService
{
    /**
     * List of tables to include in backup & restore snapshot.
     */
    protected array $targetTables = [
        'perusahaans',
        'departemens',
        'elemens',
        'sub_elemens',
        'kriterias',
        'users',
        'audit_sesis',
        'audit_details',
        'picas',
        'audit_logs',
    ];

    /**
     * Create a new system restore point / snapshot.
     */
    public function createSnapshot(?User $user, string $name, ?string $description = null, string $type = 'manual'): RestorePoint
    {
        $storageDir = storage_path('app/restore_points');
        if (!File::isDirectory($storageDir)) {
            File::makeDirectory($storageDir, 0755, true, true);
        }

        $code = 'RP-' . date('Ymd-His') . '-' . strtoupper(substr(uniqid(), -4));
        $filename = $code . '.json';
        $fullPath = $storageDir . DIRECTORY_SEPARATOR . $filename;
        $relativePath = 'restore_points/' . $filename;

        $tableCounts = [];
        $snapshotData = [
            'meta' => [
                'code'        => $code,
                'name'        => $name,
                'description' => $description,
                'type'        => $type,
                'created_by'  => $user ? ['id' => $user->id, 'name' => $user->name, 'username' => $user->username] : null,
                'created_at'  => now()->toIso8601String(),
                'version'     => 'SMKP-1.0',
            ],
            'tables' => [],
        ];

        foreach ($this->targetTables as $table) {
            try {
                $rows = DB::table($table)->get()->map(function ($row) {
                    return (array) $row;
                })->toArray();

                $tableCounts[$table] = count($rows);
                $snapshotData['tables'][$table] = $rows;
            } catch (Exception $e) {
                $tableCounts[$table] = 0;
                $snapshotData['tables'][$table] = [];
            }
        }

        $jsonContent = json_encode($snapshotData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        File::put($fullPath, $jsonContent);

        $bytes = File::size($fullPath);
        $fileSize = $this->formatBytes($bytes);
        $checksum = hash_file('sha256', $fullPath);

        $restorePoint = RestorePoint::create([
            'user_id'      => $user ? $user->id : null,
            'kode'         => $code,
            'nama'         => $name,
            'deskripsi'    => $description,
            'tipe'         => $type,
            'file_path'    => $relativePath,
            'file_size'    => $fileSize,
            'table_counts' => $tableCounts,
            'checksum'     => $checksum,
        ]);

        if ($user) {
            AuditLog::create([
                'user_id'         => $user->id,
                'modul'           => 'Restore Point',
                'tindakan'        => "Membuat Restore Point: {$name} ({$code})",
                'data_lama'       => null,
                'data_baru'       => [
                    'kode'         => $code,
                    'nama'         => $name,
                    'tipe'         => $type,
                    'file_size'    => $fileSize,
                    'table_counts' => $tableCounts,
                ],
                'waktu_perubahan' => now(),
            ]);
        }

        return $restorePoint;
    }

    /**
     * Restore system state from a specific restore point snapshot.
     */
    public function restoreSnapshot(RestorePoint $restorePoint, ?User $user = null): array
    {
        $fullPath = storage_path('app/' . $restorePoint->file_path);

        if (!File::exists($fullPath)) {
            throw new Exception("File snapshot untuk restore point {$restorePoint->kode} tidak ditemukan di server.");
        }

        // Verify checksum
        $currentChecksum = hash_file('sha256', $fullPath);
        if ($restorePoint->checksum && $restorePoint->checksum !== $currentChecksum) {
            throw new Exception("Integritas file snapshot gagal diverifikasi (Checksum mismatch).");
        }

        $json = File::get($fullPath);
        $snapshotData = json_decode($json, true);

        if (!isset($snapshotData['tables']) || !is_array($snapshotData['tables'])) {
            throw new Exception("Format isi file snapshot tidak valid.");
        }

        // Auto create safety backup before rolling back
        if ($user) {
            $this->createSnapshot(
                $user,
                "Pre-Rollback Safety Snapshot",
                "Snapshot otomatis cadangan keselamatan sebelum memulihkan ke: {$restorePoint->nama} ({$restorePoint->kode})",
                'auto_prerollback'
            );
        }

        $restoredSummary = [];

        // Disable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            DB::beginTransaction();

            foreach ($this->targetTables as $table) {
                if (isset($snapshotData['tables'][$table])) {
                    // Truncate current table
                    DB::table($table)->truncate();

                    $rows = $snapshotData['tables'][$table];
                    $restoredSummary[$table] = count($rows);

                    // Insert rows in chunks of 200
                    if (!empty($rows)) {
                        $chunks = array_chunk($rows, 200);
                        foreach ($chunks as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            throw new Exception("Gagal melakukan proses restore data: " . $e->getMessage());
        }

        // Re-enable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        if ($user) {
            AuditLog::create([
                'user_id'         => $user->id,
                'modul'           => 'Restore Point',
                'tindakan'        => "Melakukan ROLLBACK / Restore ke Restore Point: {$restorePoint->nama} ({$restorePoint->kode})",
                'data_lama'       => null,
                'data_baru'       => [
                    'restored_kode'    => $restorePoint->kode,
                    'restored_name'    => $restorePoint->nama,
                    'restored_summary' => $restoredSummary,
                ],
                'waktu_perubahan' => now(),
            ]);
        }

        return $restoredSummary;
    }

    /**
     * Delete a restore point and its associated physical snapshot file.
     */
    public function deleteSnapshot(RestorePoint $restorePoint, ?User $user = null): bool
    {
        $fullPath = storage_path('app/' . $restorePoint->file_path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        $kode = $restorePoint->kode;
        $nama = $restorePoint->nama;
        $restorePoint->delete();

        if ($user) {
            AuditLog::create([
                'user_id'         => $user->id,
                'modul'           => 'Restore Point',
                'tindakan'        => "Menghapus Restore Point: {$nama} ({$kode})",
                'data_lama'       => ['kode' => $kode, 'nama' => $nama],
                'data_baru'       => null,
                'waktu_perubahan' => now(),
            ]);
        }

        return true;
    }

    /**
     * Helper to format bytes into readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
