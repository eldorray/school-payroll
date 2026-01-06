@extends('layouts.app')

@section('title', 'Backup & Restore Database')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Backup & Restore Database</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Kelola backup database untuk keamanan data</p>
    </div>

    <!-- Create Backup Section -->
    <x-ui.card class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-[hsl(var(--foreground))]">Buat Backup Baru</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))]">Simpan salinan database saat ini</p>
            </div>
            <form action="{{ route('backups.backup') }}" method="POST">
                @csrf
                <x-ui.button type="submit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Buat Backup Sekarang
                </x-ui.button>
            </form>
        </div>
    </x-ui.card>

    <!-- Restore from Upload Section -->
    <x-ui.card class="mb-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-[hsl(var(--foreground))]">Restore dari File</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))]">Upload file backup untuk mengembalikan database</p>
            </div>
            <form action="{{ route('backups.restore') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2" onsubmit="return confirm('PERINGATAN: Ini akan mengganti semua data saat ini dengan data dari backup. Lanjutkan?');">
                @csrf
                <input type="file" name="backup_file" accept=".sqlite" class="input" required>
                <x-ui.button type="submit" variant="outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Restore
                </x-ui.button>
            </form>
        </div>
    </x-ui.card>

    <!-- Existing Backups List -->
    <x-ui.card>
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-[hsl(var(--foreground))]">Daftar Backup</h2>
            <p class="text-sm text-[hsl(var(--muted-foreground))]">Backup yang tersimpan di server</p>
        </div>
        
        @if(count($backups) > 0)
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Ukuran</th>
                            <th>Dibuat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-[hsl(var(--muted-foreground))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                        </svg>
                                        <span class="font-medium text-[hsl(var(--foreground))]">{{ $backup['filename'] }}</span>
                                    </div>
                                </td>
                                <td class="text-[hsl(var(--muted-foreground))]">{{ $backup['size'] }}</td>
                                <td class="text-[hsl(var(--muted-foreground))]">{{ $backup['created_at'] }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('backups.download', $backup['filename']) }}">
                                            <x-ui.button variant="outline" size="sm" title="Download">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </x-ui.button>
                                        </a>
                                        <form action="{{ route('backups.restore') }}" method="POST" class="inline-block" onsubmit="return confirm('PERINGATAN: Ini akan mengganti semua data saat ini. Lanjutkan restore dari {{ $backup['filename'] }}?');">
                                            @csrf
                                            <input type="hidden" name="filename" value="{{ $backup['filename'] }}">
                                            <x-ui.button type="submit" variant="outline" size="sm" title="Restore">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </x-ui.button>
                                        </form>
                                        <form action="{{ route('backups.delete', $backup['filename']) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus backup {{ $backup['filename'] }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" variant="destructive" size="sm" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-[hsl(var(--muted-foreground))] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                <p class="text-[hsl(var(--muted-foreground))]">Belum ada backup tersimpan.</p>
            </div>
        @endif
    </x-ui.card>

    <!-- Warning Note -->
    <x-ui.alert variant="warning" class="mt-6">
        <strong>Peringatan:</strong> Proses restore akan mengganti SEMUA data yang ada saat ini dengan data dari backup. Pastikan Anda membuat backup baru sebelum melakukan restore.
    </x-ui.alert>
@endsection
