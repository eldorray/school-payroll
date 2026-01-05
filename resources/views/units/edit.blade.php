@extends('layouts.app')

@section('title', 'Pengaturan Unit')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Pengaturan Unit</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Kelola informasi unit sekolah dan kepala sekolah</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Form Card -->
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-[hsl(var(--primary))]/10">
                        <svg class="w-5 h-5 text-[hsl(var(--primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">Informasi Unit</h3>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Edit data unit sekolah</p>
                    </div>
                </div>
            </x-slot:header>
            
            <form method="POST" action="{{ route('units.update') }}">
                @csrf
                @method('PATCH')

                <div class="space-y-5">
                    <!-- Unit Name -->
                    <div>
                        <x-ui.input 
                            id="name" 
                            name="name" 
                            label="Nama Unit / Sekolah"
                            :value="old('name', $unit->name)"
                            placeholder="Contoh: SD Islam Terpadu Al-Ikhlas"
                            required
                        />
                        @error('name')
                            <p class="text-sm text-[hsl(var(--destructive))] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <x-ui.input 
                            id="location" 
                            name="location" 
                            label="Tempat (Lokasi)"
                            :value="old('location', $unit->location)"
                            placeholder="Contoh: Cirebon"
                            required
                        />
                        @error('location')
                            <p class="text-sm text-[hsl(var(--destructive))] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Signature Date -->
                    <div>
                        <x-ui.input 
                            type="date"
                            id="signature_date" 
                            name="signature_date" 
                            label="Tanggal Tanda Tangan"
                            :value="old('signature_date', $unit->signature_date ? $unit->signature_date->format('Y-m-d') : '')"
                            required
                        />
                        @error('signature_date')
                            <p class="text-sm text-[hsl(var(--destructive))] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Principal Name -->
                    <div>
                        <x-ui.input 
                            id="principal_name" 
                            name="principal_name" 
                            label="Nama Kepala Sekolah"
                            :value="old('principal_name', $unit->principal_name)"
                            placeholder="Contoh: Drs. H. Ahmad Suryadi, M.Pd"
                            required
                        />
                        @error('principal_name')
                            <p class="text-sm text-[hsl(var(--destructive))] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-6 border-t border-[hsl(var(--border))]">
                    <x-ui.button type="submit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Perubahan
                    </x-ui.button>
                    <a href="{{ route('dashboard') }}">
                        <x-ui.button type="button" variant="outline">Batal</x-ui.button>
                    </a>
                </div>
            </form>
        </x-ui.card>

        <!-- Preview Card -->
        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-[hsl(var(--secondary))]">
                        <svg class="w-5 h-5 text-[hsl(var(--secondary-foreground))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">Preview Tanda Tangan</h3>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Tampilan pada slip gaji</p>
                    </div>
                </div>
            </x-slot:header>
            
            <div class="bg-[hsl(var(--secondary))]/30 rounded-lg p-6">
                <div class="text-right space-y-1">
                    <p class="text-sm text-[hsl(var(--foreground))]">
                        {{ $unit->location ?? 'Cirebon' }}, {{ $unit->signature_date ? $unit->signature_date->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y') }}
                    </p>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Kepala Sekolah</p>
                    
                    <div class="h-16"></div>
                    
                    <p class="text-sm font-semibold text-[hsl(var(--foreground))] underline underline-offset-4">
                        {{ $unit->principal_name ?? '________________________' }}
                    </p>
                </div>
            </div>

            <x-ui.alert type="info" class="mt-4">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Tanda tangan ini akan ditampilkan di bagian bawah slip gaji guru.</span>
            </x-ui.alert>
        </x-ui.card>
    </div>
@endsection
