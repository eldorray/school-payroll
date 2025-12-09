@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="page-title" style="margin-bottom: 0;">Payroll Records</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('payrolls.report', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank" class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Monthly Report
            </a>
            <a href="{{ route('payrolls.print_all', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank" class="btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print All Slips
            </a>
            <a href="{{ route('payrolls.create') }}" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Process Payroll
            </a>
        </div>
    </div>

    @if(!$activeYear)
        <div class="alert" style="background: rgba(245, 158, 11, 0.15); color: #92400e; border: 1px solid rgba(245, 158, 11, 0.3);">
            <strong>Warning:</strong> No active Academic Year found. Please activate one to manage payrolls.
        </div>
    @endif

    <!-- Filters -->
    <div class="glass-card p-5 mb-6">
        <form method="GET" action="{{ route('payrolls.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Month</label>
                <select name="month" class="input-modern" style="width: auto; min-width: 150px;">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ $i == $selectedMonth ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">Year</label>
                <input type="number" name="year" value="{{ $selectedYear }}" class="input-modern" style="width: 100px;">
            </div>
            <button type="submit" class="btn-primary">
                Apply Filter
            </button>
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Hours</th>
                    <th>Attendance</th>
                    <th>Total Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payrolls as $payroll)
                    <tr>
                        <td>
                            <div class="font-medium">{{ $payroll->teacher->name }}</div>
                            <div class="text-xs" style="color: var(--text-secondary);">{{ $payroll->teacher->position ?? 'Teacher' }}</div>
                        </td>
                        <td>
                            <span class="font-medium">{{ $payroll->teaching_hours }}</span> hrs
                        </td>
                        <td>
                            <span class="font-medium">{{ $payroll->attendance_days }}</span> days
                        </td>
                        <td>
                            <span class="font-semibold" style="color: #22c55e;">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <a href="{{ route('payrolls.show', $payroll) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;" target="_blank">
                                View Slip
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8" style="color: var(--text-secondary);">
                            No payroll records found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
