@extends('layouts.app')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Tambah Tahun Ajaran</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Isi data tahun ajaran dan pengaturan tarif gaji</p>
    </div>

    <x-ui.card class="max-w-2xl">
        <form action="{{ route('academic-years.store') }}" method="POST">
            @csrf
            
            <x-ui.input 
                name="name" 
                label="Nama Tahun Ajaran" 
                placeholder="cth: 2025/2026"
                :error="$errors->first('name')"
                class="mb-4"
                required 
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.input 
                    type="date"
                    name="start_date" 
                    label="Tanggal Mulai"
                    :error="$errors->first('start_date')"
                    required 
                />
                <x-ui.input 
                    type="date"
                    name="end_date" 
                    label="Tanggal Selesai"
                    :error="$errors->first('end_date')"
                    required 
                />
            </div>

            <hr class="my-6 border-[hsl(var(--border))]">
            <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">Pengaturan Tarif Gaji</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <x-ui.input 
                    type="number"
                    name="teaching_rate" 
                    label="Tarif Mengajar / Jam"
                    placeholder="cth: 20000"
                    :error="$errors->first('teaching_rate')"
                    required 
                />
                <x-ui.input 
                    type="number"
                    name="transport_rate" 
                    label="Tarif Transport / Hari"
                    placeholder="cth: 15000"
                    :error="$errors->first('transport_rate')"
                    required 
                />
                <x-ui.input 
                    type="number"
                    name="masa_kerja_rate" 
                    label="Tarif Masa Kerja / Tahun"
                    placeholder="cth: 10000"
                    :error="$errors->first('masa_kerja_rate')"
                    required 
                />
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-[hsl(var(--border))]">
                <x-ui.button type="submit">
                    Simpan Tahun Ajaran
                </x-ui.button>
                <a href="{{ route('academic-years.index') }}" class="text-sm text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">
                    Batal
                </a>
            </div>
        </form>
    </x-ui.card>
@endsection
