<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        
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
                if ($file->getExtension() === 'sqlite') {
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
     * Create a new backup
     */
    public function backup()
    {
        $dbPath = database_path('database.sqlite');
        
        if (!File::exists($dbPath)) {
            return back()->with('error', 'File database tidak ditemukan.');
        }

        $timestamp = date('Y-m-d_H-i-s');
        $backupFilename = "backup_{$timestamp}.sqlite";
        $backupFullPath = $this->backupPath . '/' . $backupFilename;

        try {
            File::copy($dbPath, $backupFullPath);
            return back()->with('success', "Backup berhasil dibuat: {$backupFilename}");
        } catch (\Exception $e) {
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

            $backupContent = File::get($filePath);
        } else {
            // Restore from uploaded file
            $request->validate([
                'backup_file' => 'required|file|max:102400', // Max 100MB
            ]);

            $backupContent = file_get_contents($request->file('backup_file')->path());
        }

        $dbPath = database_path('database.sqlite');

        // Create a safety backup before restoring
        $safetyBackup = $this->backupPath . '/pre_restore_' . date('Y-m-d_H-i-s') . '.sqlite';
        
        try {
            // Backup current database first
            if (File::exists($dbPath)) {
                File::copy($dbPath, $safetyBackup);
            }

            // Restore the backup
            File::put($dbPath, $backupContent);

            return back()->with('success', 'Database berhasil di-restore! Backup sebelumnya tersimpan di: ' . basename($safetyBackup));
        } catch (\Exception $e) {
            // Rollback if failed
            if (File::exists($safetyBackup)) {
                File::copy($safetyBackup, $dbPath);
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
