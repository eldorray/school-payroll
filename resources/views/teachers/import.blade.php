@extends('layouts.app')

@section('title', 'Import Data Guru')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Import Data Guru</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Upload file Excel untuk mengimpor data guru secara massal</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Upload Form -->
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">Upload File Excel</h3>
            </x-slot:header>
            
            <form action="{{ route('teachers.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))] block mb-2">Pilih File</label>
                    <input 
                        type="file" 
                        name="file" 
                        accept=".xlsx,.xls,.csv"
                        class="input w-full file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-[hsl(var(--primary))] file:text-[hsl(var(--primary-foreground))] hover:file:bg-[hsl(var(--primary))]/90"
                        required
                    >
                    @error('file')
                        <p class="text-sm text-[hsl(var(--destructive))] mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-[hsl(var(--muted-foreground))] mt-2">
                        Format yang didukung: .xlsx, .xls, .csv (Maks. 2MB)
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <x-ui.button type="submit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import Data
                    </x-ui.button>
                    <a href="{{ route('teachers.index') }}" class="text-sm text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">
                        Batal
                    </a>
                </div>
            </form>
        </x-ui.card>

        <!-- Format Guide -->
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">Format File Excel</h3>
            </x-slot:header>
            
            <p class="text-sm text-[hsl(var(--muted-foreground))] mb-4">
                Pastikan file Excel Anda memiliki kolom dengan header berikut (baris pertama):
            </p>
            
            <div class="table-wrapper mb-4">
                <table class="table text-sm">
                    <thead>
                        <tr>
                            <th>Nama Kolom</th>
                            <th>Keterangan</th>
                            <th>Wajib</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code class="px-2 py-1 bg-[hsl(var(--secondary))] rounded text-xs">Nama</code></td>
                            <td>Nama lengkap guru</td>
                            <td><x-ui.badge variant="destructive">Ya</x-ui.badge></td>
                        </tr>
                        <tr>
                            <td><code class="px-2 py-1 bg-[hsl(var(--secondary))] rounded text-xs">Jabatan</code></td>
                            <td>Jabatan (Guru, Kepsek, dll)</td>
                            <td><x-ui.badge variant="secondary">Tidak</x-ui.badge></td>
                        </tr>
                        <tr>
                            <td><code class="px-2 py-1 bg-[hsl(var(--secondary))] rounded text-xs">NIP</code></td>
                            <td>Nomor Induk Pegawai</td>
                            <td><x-ui.badge variant="secondary">Tidak</x-ui.badge></td>
                        </tr>
                        <tr>
                            <td><code class="px-2 py-1 bg-[hsl(var(--secondary))] rounded text-xs">Tanggal_Bergabung</code></td>
                            <td>Format: YYYY-MM-DD</td>
                            <td><x-ui.badge variant="secondary">Tidak</x-ui.badge></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-ui.alert type="info">
                <strong>Tips:</strong> Header kolom tidak case-sensitive dan akan dikonversi ke lowercase dengan underscore (Tanggal Bergabung → tanggal_bergabung).
            </x-ui.alert>

            <div class="mt-4">
                <a href="{{ route('teachers.import.template') }}">
                    <x-ui.button variant="outline" class="w-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Template Excel
                    </x-ui.button>
                </a>
            </div>
        </x-ui.card>
    </div>
@endsection
