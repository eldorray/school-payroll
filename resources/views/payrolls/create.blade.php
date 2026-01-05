@extends('layouts.app')

@section('title', 'Proses Gaji')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Proses Penggajian</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Hitung dan simpan gaji bulanan guru</p>
    </div>

    <x-ui.card>
        <div class="mb-6 p-4 rounded-lg bg-[hsl(var(--secondary))]">
            <h3 class="font-semibold text-[hsl(var(--foreground))]">Tahun Ajaran Aktif: <span class="text-[hsl(var(--primary))]">{{ $activeYear->name }}</span></h3>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">
                Tarif Standar: Mengajar (Rp {{ number_format($activeYear->payrollSettings->teaching_rate_per_hour, 0, ',', '.') }}/jam), 
                Transport (Rp {{ number_format($activeYear->payrollSettings->transport_rate_per_visit, 0, ',', '.') }}/hari)
            </p>
        </div>

        <form action="{{ route('payrolls.store') }}" method="POST">
            @csrf
            
            <div class="flex flex-wrap gap-4 mb-6">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Bulan</label>
                    <select name="month" class="input w-40">
                        @php
                            $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Tahun</label>
                    <input type="number" name="year" value="{{ $currentYear }}" class="input w-24">
                </div>
                <div class="space-y-2 flex-1 min-w-[200px]">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Nama Batch <span class="text-[hsl(var(--muted-foreground))] font-normal">(Opsional)</span></label>
                    <input type="text" name="batch_name" class="input" placeholder="Contoh: Gaji Pokok, THR, Bonus, dll">
                </div>
            </div>

            <div class="table-wrapper mb-6">
                <table class="table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle">Guru</th>
                            <th rowspan="2" class="text-center align-middle">Transport<br><span class="text-xs font-normal">(Hari)</span></th>
                            <th colspan="4" class="text-center border-b border-[hsl(var(--border))]">Potongan</th>
                            <th rowspan="2" class="text-right align-middle">Tunjangan<br><span class="text-xs font-normal">(Est)</span></th>
                        </tr>
                        <tr>
                            <th class="text-center text-xs">Insentif</th>
                            <th class="text-center text-xs">BPJS</th>
                            <th class="text-center text-xs">Terlambat</th>
                            <th class="text-center text-xs">Lainnya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers as $teacher)
                        @php 
                            $allowanceTotal = \App\Models\TeacherAllowance::where('teacher_id', $teacher->id)
                                ->where('academic_year_id', $activeYear->id)
                                ->sum('amount');
                            $annualSetting = $teacher->annualSettings->where('academic_year_id', $activeYear->id)->first();
                            $hours = $annualSetting ? $annualSetting->teaching_hours_per_month : 0;
                            $annualBpjs = $annualSetting ? $annualSetting->bpjs_amount : 0;
                        @endphp
                        <tr>
                            <td>
                                <div class="font-medium text-[hsl(var(--foreground))]">{{ $teacher->name }}</div>
                                <div class="text-xs text-[hsl(var(--muted-foreground))]">{{ $teacher->position ?? 'Guru' }}</div>
                                <div class="text-xs text-[hsl(var(--muted-foreground))] mt-1">
                                    Jam: {{ $hours }} | BPJS: {{ number_format($annualBpjs, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="number" name="attendance[{{ $teacher->id }}][days]" class="input w-16 text-center" min="0" placeholder="0">
                            </td>
                            <td class="text-center">
                                <input type="number" name="attendance[{{ $teacher->id }}][deductions][incentive_deduction]" class="input w-20 text-right text-[hsl(var(--destructive))]" min="0" placeholder="0">
                            </td>
                            <td class="text-center bg-[hsl(var(--secondary))]">
                                <span class="text-xs text-[hsl(var(--muted-foreground))]">(Auto)</span>
                            </td>
                            <td class="text-center">
                                <input type="number" name="attendance[{{ $teacher->id }}][deductions][transport_deduction]" class="input w-20 text-right text-[hsl(var(--destructive))]" min="0" placeholder="0">
                            </td>
                            <td class="text-center">
                                <input type="number" name="attendance[{{ $teacher->id }}][deductions][other_deduction]" class="input w-20 text-right text-[hsl(var(--destructive))]" min="0" placeholder="0">
                            </td>
                            <td class="text-right text-[hsl(var(--muted-foreground))]">
                                {{ number_format($allowanceTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-[hsl(var(--border))]">
                <x-ui.button type="submit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Hitung & Simpan Gaji
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
