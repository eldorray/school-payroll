@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-[hsl(var(--foreground))] mb-1">Dashboard</h1>
        @php $unit = \App\Models\Unit::find(session('unit_id')); @endphp
        @if($unit)
            <p class="text-sm text-[hsl(var(--muted-foreground))]">{{ $unit->name }}</p>
        @endif
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Active Teachers -->
        <x-ui.card class="animate-fade-up">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Guru Aktif</p>
                    <p class="text-3xl font-bold text-[hsl(var(--foreground))] mt-1">
                        {{ \App\Models\Teacher::where('unit_id', session('unit_id'))->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </x-ui.card>

        <!-- Active Academic Year -->
        <x-ui.card class="animate-fade-up delay-100">
            <div class="flex items-center justify-between">
                <div>
                    @php $activeYear = \App\Models\AcademicYear::where('is_active', true)->first(); @endphp
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Tahun Ajaran Aktif</p>
                    <p class="text-xl font-bold text-[hsl(var(--foreground))] mt-1">
                        {{ $activeYear ? $activeYear->name : 'Belum diatur' }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </x-ui.card>

        <!-- Payrolls This Month -->
        <x-ui.card class="animate-fade-up delay-200">
            <div class="flex items-center justify-between">
                <div>
                    @php
                        $prevMonth = now()->subMonth();
                    @endphp
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Payroll {{ $prevMonth->translatedFormat('F') }}</p>
                    <p class="text-3xl font-bold text-[hsl(var(--foreground))] mt-1">
                        {{ \App\Models\Payroll::where('unit_id', session('unit_id'))->where('month', $prevMonth->month)->where('year', $prevMonth->year)->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Quick Actions -->
    <x-ui.card class="animate-fade-up delay-300">
        <x-slot:header>
            <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">Aksi Cepat</h3>
        </x-slot:header>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('payrolls.create') }}">
                <x-ui.button>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Proses Gaji Bulanan
                </x-ui.button>
            </a>
            <a href="{{ route('teachers.create') }}">
                <x-ui.button variant="secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Tambah Guru Baru
                </x-ui.button>
            </a>
            <a href="{{ route('academic-years.create') }}">
                <x-ui.button variant="outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Tambah Tahun Ajaran
                </x-ui.button>
            </a>
        </div>
    </x-ui.card>
@endsection
