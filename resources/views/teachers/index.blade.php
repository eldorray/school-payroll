@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="page-title" style="margin-bottom: 0;">Teachers</h1>
        <a href="{{ route('teachers.create') }}" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Teacher
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>NIP</th>
                    <th>Joined At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($teachers as $teacher)
                    <tr>
                        <td>
                            <div class="font-medium">{{ $teacher->name }}</div>
                        </td>
                        <td>
                            <span class="text-sm" style="color: var(--text-secondary);">{{ $teacher->position ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="text-sm" style="color: var(--text-secondary);">{{ $teacher->nip ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="text-sm" style="color: var(--text-secondary);">{{ $teacher->joined_at ? $teacher->joined_at->format('d M Y') : '-' }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('teachers.edit', $teacher) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                                    Edit
                                </a>
                                <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this teacher?');">
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
                            No teachers found. <a href="{{ route('teachers.create') }}" style="color: var(--accent);">Add one now</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
