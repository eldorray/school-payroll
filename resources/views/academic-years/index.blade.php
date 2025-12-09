@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="page-title" style="margin-bottom: 0;">Academic Years</h1>
        <a href="{{ route('academic-years.create') }}" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Year
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($years as $year)
                    <tr>
                        <td>
                            <div class="font-medium">{{ $year->name }}</div>
                        </td>
                        <td>
                            <span class="text-sm" style="color: var(--text-secondary);">{{ $year->start_date->format('d M Y') }}</span>
                        </td>
                        <td>
                            <span class="text-sm" style="color: var(--text-secondary);">{{ $year->end_date->format('d M Y') }}</span>
                        </td>
                        <td>
                            @if($year->is_active)
                                <span style="display: inline-flex; align-items: center; padding: 4px 10px; font-size: 12px; font-weight: 500; border-radius: 20px; background: rgba(52, 199, 89, 0.15); color: #1d7a3d;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #22c55e; margin-right: 6px;"></span>
                                    Active
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; padding: 4px 10px; font-size: 12px; font-weight: 500; border-radius: 20px; background: rgba(0, 0, 0, 0.05); color: var(--text-secondary);">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('academic-years.edit', $year) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                    Edit
                                </a>
                                
                                @if(!$year->is_active)
                                    <form action="{{ route('academic-years.activate', $year) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 12px; color: #22c55e; border-color: rgba(34, 197, 94, 0.3);">
                                            Activate
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('academic-years.destroy', $year) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 12px; color: #ef4444; border-color: rgba(239, 68, 68, 0.3);">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8" style="color: var(--text-secondary);">
                            No academic years found. <a href="{{ route('academic-years.create') }}" style="color: var(--accent);">Add one now</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
