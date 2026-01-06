@extends('layouts.app')

@section('title', 'Edit Batch Gaji')

@section('content')
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-[hsl(var(--muted-foreground))] mb-2">
            <a href="{{ route('payrolls.index', ['month' => $batch->month, 'year' => $batch->year]) }}" class="hover:text-[hsl(var(--foreground))]">Penggajian</a>
            <span>/</span>
            <span>Edit Batch</span>
        </div>
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Edit Batch Penggajian</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $batch->period }} - {{ $batch->display_name }}</p>
    </div>

    <x-ui.card>
        <div class="mb-6 p-4 rounded-lg bg-[hsl(var(--secondary))]">
            <h3 class="font-semibold text-[hsl(var(--foreground))]">Tahun Ajaran: <span class="text-[hsl(var(--primary))]">{{ $activeYear->name }}</span></h3>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">
                Tarif Standar: Mengajar (Rp {{ number_format($settings->teaching_rate_per_hour, 0, ',', '.') }}/jam), 
                Transport (Rp {{ number_format($settings->transport_rate_per_visit, 0, ',', '.') }}/hari)
            </p>
        </div>

        <form action="{{ route('payrolls.batch.update', $batch) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-6">
                <label class="text-sm font-medium text-[hsl(var(--foreground))] block mb-2">Nama Batch</label>
                <input type="text" name="batch_name" value="{{ old('batch_name', $batch->name) }}" class="input max-w-md" placeholder="Contoh: Gaji Pokok, THR, Bonus, dll">
            </div>

            <div class="table-wrapper mb-6">
                <table class="table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle">Guru</th>
                            <th rowspan="2" class="text-center align-middle">Transport<br><span class="text-xs font-normal">(Hari)</span></th>
                            <th colspan="4" class="text-center border-b border-[hsl(var(--border))]">Potongan</th>
                            <th rowspan="2" class="text-right align-middle">Est. Total</th>
                        </tr>
                        <tr>
                            <th class="text-center text-xs">Insentif</th>
                            <th class="text-center text-xs">BPJS</th>
                            <th class="text-center text-xs">Terlambat</th>
                            <th class="text-center text-xs">Lainnya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($batch->payrolls as $payroll)
                        @php 
                            $teacher = $payroll->teacher;
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
                                <input type="number" name="attendance[{{ $payroll->id }}][days]" value="{{ old("attendance.{$payroll->id}.days", $payroll->attendance_days) }}" class="input w-16 text-center" min="0">
                            </td>
                            <td class="text-center">
                                <input type="number" name="attendance[{{ $payroll->id }}][deductions][incentive_deduction]" value="{{ old("attendance.{$payroll->id}.deductions.incentive_deduction", $payroll->incentive_deduction_amount) }}" class="input w-20 text-right text-[hsl(var(--destructive))]" min="0">
                            </td>
                            <td class="text-center bg-[hsl(var(--secondary))]">
                                <span class="text-xs text-[hsl(var(--muted-foreground))]">(Auto)</span>
                            </td>
                            @php
                                $details = $payroll->details ?? [];
                                $lateCount = $details['breakdown']['deductions']['late_count'] ?? 0;
                            @endphp
                            <td class="text-center">
                                <input type="number" name="attendance[{{ $payroll->id }}][deductions][late_count]" value="{{ old("attendance.{$payroll->id}.deductions.late_count", $lateCount) }}" class="input w-16 text-center text-[hsl(var(--destructive))]" min="0" title="Jumlah keterlambatan">
                            </td>
                            <td class="text-center">
                                <input type="number" name="attendance[{{ $payroll->id }}][deductions][other_deduction]" value="{{ old("attendance.{$payroll->id}.deductions.other_deduction", $payroll->other_deduction_amount) }}" class="input w-20 text-right text-[hsl(var(--destructive))]" min="0">
                            </td>
                            <td class="text-right text-green-600 font-semibold">
                                Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-[hsl(var(--border))]">
                <a href="{{ route('payrolls.index', ['month' => $batch->month, 'year' => $batch->year]) }}">
                    <x-ui.button type="button" variant="outline">
                        Batal
                    </x-ui.button>
                </a>
                <x-ui.button type="submit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
