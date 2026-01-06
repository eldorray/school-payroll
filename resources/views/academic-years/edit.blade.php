@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Edit Tahun Ajaran</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $academicYear->name }}</p>
    </div>

    <x-ui.card class="max-w-2xl">
        <form action="{{ route('academic-years.update', $academicYear) }}" method="POST">
            @csrf
            @method('PUT')
            
            <x-ui.input 
                name="name" 
                label="Nama Tahun Ajaran" 
                :value="$academicYear->name"
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
                    :value="$academicYear->start_date->format('Y-m-d')"
                    :error="$errors->first('start_date')"
                    required 
                />
                <x-ui.input 
                    type="date"
                    name="end_date" 
                    label="Tanggal Selesai"
                    :value="$academicYear->end_date->format('Y-m-d')"
                    :error="$errors->first('end_date')"
                    required 
                />
            </div>

            <hr class="my-6 border-[hsl(var(--border))]">
            <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">Pengaturan Tarif Gaji</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.input 
                    type="number"
                    name="teaching_rate" 
                    label="Tarif Mengajar / Jam"
                    :value="$academicYear->payrollSettings->teaching_rate_per_hour ?? 0"
                    :error="$errors->first('teaching_rate')"
                    required 
                />
                <x-ui.input 
                    type="number"
                    name="transport_rate" 
                    label="Tarif Transport / Hari"
                    :value="$academicYear->payrollSettings->transport_rate_per_visit ?? 0"
                    :error="$errors->first('transport_rate')"
                    required 
                />
                <x-ui.input 
                    type="number"
                    name="masa_kerja_rate" 
                    label="Tarif Masa Kerja / Tahun"
                    :value="$academicYear->payrollSettings->masa_kerja_rate_per_year ?? 0"
                    :error="$errors->first('masa_kerja_rate')"
                    required 
                />
                <x-ui.input 
                    type="number"
                    name="late_deduction_rate" 
                    label="Tarif Potongan Terlambat"
                    :value="$academicYear->payrollSettings->late_deduction_rate ?? 0"
                    :error="$errors->first('late_deduction_rate')"
                    required 
                />
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-[hsl(var(--border))]">
                <x-ui.button type="submit">
                    Perbarui Tahun Ajaran
                </x-ui.button>
                <a href="{{ route('academic-years.index') }}" class="text-sm text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">
                    Batal
                </a>
            </div>
        </form>
    </x-ui.card>
@endsection
