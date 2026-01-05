@extends('layouts.app')

@section('title', 'Penggajian')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Data Penggajian</h1>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Kelola gaji bulanan guru dan pengajar</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('payrolls.create') }}">
                <x-ui.button>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Proses Gaji Baru
                </x-ui.button>
            </a>
        </div>
    </div>

    @if(!$activeYear)
        <x-ui.alert type="warning" class="mb-6">
            <strong>Peringatan:</strong> Tidak ada Tahun Ajaran aktif. Silakan aktifkan salah satu untuk mengelola penggajian.
        </x-ui.alert>
    @endif

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('payrolls.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="space-y-2">
                <label class="text-sm font-medium text-[hsl(var(--foreground))]">Bulan</label>
                <select name="month" class="input w-auto min-w-[150px]">
                    @php
                        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    @endphp
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $num == $selectedMonth ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-medium text-[hsl(var(--foreground))]">Tahun</label>
                <input type="number" name="year" value="{{ $selectedYear }}" class="input w-24">
            </div>
            <x-ui.button type="submit">
                Terapkan Filter
            </x-ui.button>
        </form>
    </x-ui.card>

    <!-- Batches Accordion -->
    <div class="space-y-3">
        @forelse ($batches as $batch)
            <div class="border border-[hsl(var(--border))] rounded-lg bg-[hsl(var(--card))] overflow-hidden" x-data="{ open: false }">
                <!-- Accordion Header -->
                <button 
                    @click="open = !open" 
                    class="w-full px-4 py-4 flex items-center justify-between hover:bg-[hsl(var(--secondary))]/50 transition-colors"
                >
                    <div class="flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-[hsl(var(--primary))]/10">
                            <svg class="w-5 h-5 text-[hsl(var(--primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-semibold text-[hsl(var(--foreground))]">
                                {{ $batch->name ?? 'Honor' }}
                            </h3>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">
                                Dibuat: {{ $batch->created_at->format('d M Y H:i') }} • 
                                {{ $batch->payrolls->count() }} guru • 
                                Total: <span class="font-semibold text-green-600">Rp {{ number_format($batch->total_amount, 0, ',', '.') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg 
                            class="w-5 h-5 text-[hsl(var(--muted-foreground))] transition-transform duration-200" 
                            :class="{ 'rotate-180': open }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>

                <!-- Accordion Content -->
                <div x-show="open" x-collapse class="border-t border-[hsl(var(--border))]">
                    <!-- Action Buttons -->
                    <div class="px-4 py-3 bg-[hsl(var(--secondary))]/30 flex flex-wrap gap-2">
                        <a href="{{ route('payrolls.batch.edit', $batch) }}">
                            <x-ui.button variant="outline" size="sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Batch
                            </x-ui.button>
                        </a>
                        <a href="{{ route('payrolls.report', ['batch' => $batch->id]) }}" target="_blank">
                            <x-ui.button variant="outline" size="sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Laporan
                            </x-ui.button>
                        </a>
                        <a href="{{ route('payrolls.print_all', ['batch' => $batch->id]) }}" target="_blank">
                            <x-ui.button variant="outline" size="sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Cetak Slip
                            </x-ui.button>
                        </a>
                        <form action="{{ route('payrolls.batch.destroy', $batch) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus batch ini?');">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="destructive" size="sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Hapus
                            </x-ui.button>
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th class="text-center">Jam</th>
                                    <th class="text-center">Kehadiran</th>
                                    <th class="text-right">Total Gaji</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($batch->payrolls as $payroll)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-[hsl(var(--foreground))]">{{ $payroll->teacher->name }}</div>
                                            <div class="text-xs text-[hsl(var(--muted-foreground))]">{{ $payroll->teacher->position ?? 'Guru' }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-medium">{{ $payroll->teaching_hours }}</span>
                                            <span class="text-[hsl(var(--muted-foreground))] text-xs">jam</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-medium">{{ $payroll->attendance_days }}</span>
                                            <span class="text-[hsl(var(--muted-foreground))] text-xs">hari</span>
                                        </td>
                                        <td class="text-right">
                                            <span class="font-semibold text-green-600">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('payrolls.show', $payroll) }}" target="_blank">
                                                <x-ui.button variant="outline" size="sm">Slip</x-ui.button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-[hsl(var(--secondary))]/50">
                                    <td colspan="3" class="font-semibold text-right">Total Batch:</td>
                                    <td class="text-right font-bold text-green-600">Rp {{ number_format($batch->total_amount, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <x-ui.card>
                <div class="text-center py-8 text-[hsl(var(--muted-foreground))]">
                    @php
                        $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    @endphp
                    <svg class="w-12 h-12 mx-auto mb-4 text-[hsl(var(--muted-foreground))]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="mb-2">Tidak ada data gaji untuk {{ $monthNames[$selectedMonth] }} {{ $selectedYear }}.</p>
                    <a href="{{ route('payrolls.create') }}" class="text-[hsl(var(--primary))] hover:underline font-medium">Buat penggajian baru →</a>
                </div>
            </x-ui.card>
        @endforelse
    </div>
@endsection
