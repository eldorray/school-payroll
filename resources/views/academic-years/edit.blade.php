@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Edit Tahun Ajaran</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $academicYear->name }}</p>
    </div>

    <x-ui.card class="max-w-4xl">
        <form action="{{ route('academic-years.update', $academicYear) }}" method="POST">
            @csrf
            @method('PUT')

            <x-ui.input name="name" label="Nama Tahun Ajaran" :value="$academicYear->name" placeholder="cth: 2025/2026"
                :error="$errors->first('name')" class="mb-4" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.input type="date" name="start_date" label="Tanggal Mulai" :value="$academicYear->start_date->format('Y-m-d')" :error="$errors->first('start_date')"
                    required />
                <x-ui.input type="date" name="end_date" label="Tanggal Selesai" :value="$academicYear->end_date->format('Y-m-d')" :error="$errors->first('end_date')"
                    required />
            </div>

            <hr class="my-6 border-[hsl(var(--border))]">
            <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">Pengaturan Tarif Gaji per Unit</h3>

            @foreach ($units as $unit)
                @php
                    $settings = $settingsMap->get($unit->id);
                @endphp
                <div class="mb-6 p-4 border border-[hsl(var(--border))] rounded-lg bg-[hsl(var(--muted)/0.3)]">
                    <h4 class="text-md font-medium text-[hsl(var(--foreground))] mb-4">{{ $unit->name }}</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <x-ui.input type="number" name="units[{{ $unit->id }}][teaching_rate]"
                            label="Tarif Mengajar / Jam" :value="$settings->teaching_rate_per_hour ?? 0" :error="$errors->first('units.' . $unit->id . '.teaching_rate')" required />
                        <x-ui.input type="number" name="units[{{ $unit->id }}][transport_rate]"
                            label="Tarif Transport / Hari" :value="$settings->transport_rate_per_visit ?? 0" :error="$errors->first('units.' . $unit->id . '.transport_rate')" required />
                        <x-ui.input type="number" name="units[{{ $unit->id }}][masa_kerja_rate]"
                            label="Tarif Masa Kerja / Tahun" :value="$settings->masa_kerja_rate_per_year ?? 0" :error="$errors->first('units.' . $unit->id . '.masa_kerja_rate')" required />
                        <x-ui.input type="number" name="units[{{ $unit->id }}][late_deduction_rate]"
                            label="Tarif Potongan Terlambat" :value="$settings->late_deduction_rate ?? 0" :error="$errors->first('units.' . $unit->id . '.late_deduction_rate')" />
                    </div>
                </div>
            @endforeach

            <div class="flex items-center justify-between pt-4 border-t border-[hsl(var(--border))]">
                <x-ui.button type="submit">
                    Perbarui Tahun Ajaran
                </x-ui.button>
                <a href="{{ route('academic-years.index') }}"
                    class="text-sm text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">
                    Batal
                </a>
            </div>
        </form>
    </x-ui.card>
@endsection
