@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6">Process Payroll</h2>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="mb-4">
            <h3 class="text-lg font-semibold">Active Year: <span class="text-blue-600">{{ $activeYear->name }}</span></h3>
            <p class="text-sm text-gray-600">Standard Rates: Teaching (Rp {{ number_format($activeYear->payrollSettings->teaching_rate_per_hour, 0, ',', '.') }}), Transport (Rp {{ number_format($activeYear->payrollSettings->transport_rate_per_visit, 0, ',', '.') }})</p>
        </div>

        <form action="{{ route('payrolls.store') }}" method="POST">
            @csrf
            
            <div class="flex space-x-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Month</label>
                    <select name="month" class="shadow border rounded py-2 px-3 w-40">
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Year</label>
                    <input type="number" name="year" value="{{ $currentYear }}" class="shadow border rounded py-2 px-3 w-32">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 mb-6 border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th rowspan="2" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-r text-center align-middle sticky left-0 bg-gray-50 z-10">Teacher</th>
                            <th rowspan="2" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase border-r align-middle">Transport<br>(Days)</th>
                            <th colspan="4" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase border-b">Potongan (Deductions)</th>
                            <th rowspan="2" class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase align-middle">Allowances<br>(Est)</th>
                        </tr>
                        <tr>
                            <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase border-r">Insentif</th>
                            <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase border-r">BPJS</th>
                            <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase border-r">Terlambat</th>
                            <th class="px-2 py-1 text-center text-xs font-medium text-gray-500 uppercase border-r">Lainnya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($teachers as $teacher)
                        @php 
                            $allowanceTotal = \App\Models\TeacherAllowance::where('teacher_id', $teacher->id)
                                ->where('academic_year_id', $activeYear->id)
                                ->sum('amount');
                        @endphp
                        <tr>
                            <td class="px-4 py-2 font-medium sticky left-0 bg-white border-r z-10">{{ $teacher->name }}<br><span class="text-xs text-gray-400">{{ $teacher->position ?? 'Guru' }}</span></td>
                            <td class="px-2 py-2 border-r">
                                @php
                                    $annualSetting = $teacher->annualSettings->where('academic_year_id', $activeYear->id)->first();
                                    $hours = $annualSetting ? $annualSetting->teaching_hours_per_month : 0;
                                @endphp
                                <div class="text-xs text-gray-500 mb-1">Hrs: {{ $hours }}</div>
                                @php
                                    $annualBpjs = $annualSetting ? $annualSetting->bpjs_amount : 0;
                                @endphp
                                <div class="text-xs text-gray-500 mb-1">BPJS: {{ number_format($annualBpjs, 0, ',', '.') }}</div>
                                <input type="number" name="attendance[{{ $teacher->id }}][days]" class="w-16 border rounded px-1 py-1 text-right" min="0" placeholder="0">
                            </td>
                            <!-- Deductions -->
                            <td class="px-2 py-2 border-r">
                                <input type="number" name="attendance[{{ $teacher->id }}][deductions][incentive_deduction]" class="w-24 border rounded px-1 py-1 text-right text-red-600" min="0" placeholder="0">
                            </td>
                            <!-- BPJS removed from input, used from annual setting -->
                            <td class="px-2 py-2 border-r bg-gray-50 text-center text-gray-400 text-xs">
                                (Auto)
                            </td>
                            <td class="px-2 py-2 border-r">
                                <input type="number" name="attendance[{{ $teacher->id }}][deductions][transport_deduction]" class="w-24 border rounded px-1 py-1 text-right text-red-600" min="0" placeholder="0">
                            </td>
                            <td class="px-2 py-2 border-r">
                                <input type="number" name="attendance[{{ $teacher->id }}][deductions][other_deduction]" class="w-24 border rounded px-1 py-1 text-right text-red-600" min="0" placeholder="0">
                            </td>
                            
                            <td class="px-4 py-2 text-gray-600 text-right">
                                {{ number_format($allowanceTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none shadow" type="submit">
                    Calculate & Save Payroll
                </button>
            </div>
        </form>
    </div>
@endsection
