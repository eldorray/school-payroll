<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    protected $backupPath;
    protected $dbHost;
    protected $dbPort;
    protected $dbName;
    protected $dbUser;
    protected $dbPass;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');

        // Database credentials from config
        $this->dbHost = config('database.connections.mysql.host');
        $this->dbPort = config('database.connections.mysql.port');
        $this->dbName = config('database.connections.mysql.database');
        $this->dbUser = config('database.connections.mysql.username');
        $this->dbPass = config('database.connections.mysql.password');

        // Create backup directory if not exists
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Display backup page with list of existing backups
     */
    public function index()
    {
        $backups = [];

        if (File::exists($this->backupPath)) {
            $files = File::files($this->backupPath);

            foreach ($files as $file) {
                if ($file->getExtension() === 'sql') {
                    $backups[] = [
                        'filename' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'created_at' => date('d M Y H:i:s', $file->getMTime()),
                        'timestamp' => $file->getMTime(),
                    ];
                }
            }

            // Sort by newest first
            usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        }

        return view('backups.index', compact('backups'));
    }

    /**
     * Create a new backup using mysqldump
     */
    public function backup()
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupFilename = "backup_{$timestamp}.sql";
        $backupFullPath = $this->backupPath . '/' . $backupFilename;

        try {
            // Build mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
                escapeshellarg($this->dbHost),
                escapeshellarg($this->dbPort),
                escapeshellarg($this->dbUser),
                escapeshellarg($this->dbPass),
                escapeshellarg($this->dbName),
                escapeshellarg($backupFullPath)
            );

            // Execute mysqldump
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(300); // 5 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Verify backup file was created
            if (!File::exists($backupFullPath) || File::size($backupFullPath) === 0) {
                throw new \Exception('Backup file kosong atau tidak terbuat.');
            }

            return back()->with('success', "Backup berhasil dibuat: {$backupFilename}");
        } catch (\Exception $e) {
            // Clean up failed backup file
            if (File::exists($backupFullPath)) {
                File::delete($backupFullPath);
            }
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function download($filename)
    {
        $filePath = $this->backupPath . '/' . $filename;

        if (!File::exists($filePath)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($filePath, $filename);
    }

    /**
     * Restore database from a backup
     */
    public function restore(Request $request)
    {
        $filename = $request->input('filename');

        if ($filename) {
            // Restore from existing backup
            $filePath = $this->backupPath . '/' . $filename;

            if (!File::exists($filePath)) {
                return back()->with('error', 'File backup tidak ditemukan.');
            }
        } else {
            // Restore from uploaded file
            $request->validate([
                'backup_file' => 'required|file|max:102400', // Max 100MB
            ]);

            // Save uploaded file temporarily
            $uploadedFile = $request->file('backup_file');
            $filename = 'uploaded_' . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $this->backupPath . '/' . $filename;
            $uploadedFile->move($this->backupPath, $filename);
        }

        // Create a safety backup before restoring
        $safetyBackupFilename = 'pre_restore_' . date('Y-m-d_H-i-s') . '.sql';
        $safetyBackupPath = $this->backupPath . '/' . $safetyBackupFilename;

        try {
            // Backup current database first
            $backupCommand = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
                escapeshellarg($this->dbHost),
                escapeshellarg($this->dbPort),
                escapeshellarg($this->dbUser),
                escapeshellarg($this->dbPass),
                escapeshellarg($this->dbName),
                escapeshellarg($safetyBackupPath)
            );

            $backupProcess = Process::fromShellCommandline($backupCommand);
            $backupProcess->setTimeout(300);
            $backupProcess->run();

            if (!$backupProcess->isSuccessful()) {
                throw new \Exception('Gagal membuat safety backup sebelum restore.');
            }

            // Restore the backup using mysql command
            $restoreCommand = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
                escapeshellarg($this->dbHost),
                escapeshellarg($this->dbPort),
                escapeshellarg($this->dbUser),
                escapeshellarg($this->dbPass),
                escapeshellarg($this->dbName),
                escapeshellarg($filePath)
            );

            $restoreProcess = Process::fromShellCommandline($restoreCommand);
            $restoreProcess->setTimeout(600); // 10 minutes timeout for restore
            $restoreProcess->run();

            if (!$restoreProcess->isSuccessful()) {
                throw new ProcessFailedException($restoreProcess);
            }

            return back()->with('success', 'Database berhasil di-restore! Backup sebelumnya tersimpan di: ' . $safetyBackupFilename);
        } catch (\Exception $e) {
            // Attempt rollback if we have a safety backup
            if (File::exists($safetyBackupPath)) {
                try {
                    $rollbackCommand = sprintf(
                        'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
                        escapeshellarg($this->dbHost),
                        escapeshellarg($this->dbPort),
                        escapeshellarg($this->dbUser),
                        escapeshellarg($this->dbPass),
                        escapeshellarg($this->dbName),
                        escapeshellarg($safetyBackupPath)
                    );

                    $rollbackProcess = Process::fromShellCommandline($rollbackCommand);
                    $rollbackProcess->setTimeout(600);
                    $rollbackProcess->run();
                } catch (\Exception $rollbackException) {
                    // Log rollback failure
                }
            }
            return back()->with('error', 'Gagal restore database: ' . $e->getMessage());
        }
    }

    /**
     * Delete a backup file
     */
    public function delete($filename)
    {
        $filePath = $this->backupPath . '/' . $filename;

        if (!File::exists($filePath)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        try {
            File::delete($filePath);
            return back()->with('success', "Backup {$filename} berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus backup: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
