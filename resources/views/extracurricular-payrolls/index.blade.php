@extends('layouts.app')

@section('title', 'Gaji Ekskul')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Penggajian Ekstrakurikuler</h1>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Kelola honor pembina ekskul</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('extracurricular-payrolls.report', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank">
                <x-ui.button variant="outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Cetak Laporan
                </x-ui.button>
            </a>
            <a href="{{ route('extracurricular-payrolls.print_all', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank">
                <x-ui.button variant="outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Cetak Semua Slip
                </x-ui.button>
            </a>
            <a href="{{ route('extracurricular-payrolls.create') }}">
                <x-ui.button>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Input Gaji Ekskul
                </x-ui.button>
            </a>
        </div>
    </div>

    @if(!$activeYear)
        <x-ui.alert type="warning" class="mb-6">
            <strong>Peringatan:</strong> Tidak ada Tahun Ajaran aktif.
        </x-ui.alert>
    @endif

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('extracurricular-payrolls.index') }}" class="flex flex-wrap items-end gap-4">
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

    <!-- Data Table -->
    <x-ui.card>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Bidang Ekskul</th>
                        <th class="text-right">Rupiah</th>
                        <th class="text-center">Volume</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payrolls as $index => $payroll)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="font-medium text-[hsl(var(--foreground))]">{{ $payroll->teacher->name }}</div>
                            </td>
                            <td>{{ $payroll->extracurricular->name }}</td>
                            <td class="text-right">Rp {{ number_format($payroll->rate, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $payroll->volume }}</td>
                            <td class="text-right font-semibold text-green-600">Rp {{ number_format($payroll->total, 0, ',', '.') }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('extracurricular-payrolls.show', $payroll) }}" target="_blank">
                                        <x-ui.button variant="outline" size="sm">Slip</x-ui.button>
                                    </a>
                                    <form action="{{ route('extracurricular-payrolls.destroy', $payroll) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="destructive" size="sm">Hapus</x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-[hsl(var(--muted-foreground))]">
                                @php $monthName = $months[$selectedMonth] ?? ''; @endphp
                                Tidak ada data gaji ekskul untuk {{ $monthName }} {{ $selectedYear }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($payrolls->count() > 0)
                <tfoot>
                    <tr class="bg-[hsl(var(--secondary))]/50">
                        <td colspan="5" class="text-right font-semibold">Jumlah Total:</td>
                        <td class="text-right font-bold text-green-600">Rp {{ number_format($payrolls->sum('total'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </x-ui.card>
@endsection
