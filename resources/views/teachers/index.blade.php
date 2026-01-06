@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Data Guru</h1>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Kelola data guru dan pengajar</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('teachers.import') }}">
                <x-ui.button variant="outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import Excel
                </x-ui.button>
            </a>
            <a href="{{ route('teachers.create') }}">
                <x-ui.button>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Guru
                </x-ui.button>
            </a>
        </div>
    </div>

    <x-ui.card>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>NIP</th>
                        <th>Tanggal Bergabung</th>
                        <th>Masa Kerja</th>
                        <th>Status Gaji</th>
                        <th>Tahfidz</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td>
                                <div class="font-medium text-[hsl(var(--foreground))]">{{ $teacher->name }}</div>
                            </td>
                            <td>
                                <span class="text-sm text-[hsl(var(--muted-foreground))]">{{ $teacher->position ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="text-sm text-[hsl(var(--muted-foreground))]">{{ $teacher->nip ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="text-sm text-[hsl(var(--muted-foreground))]">{{ $teacher->joined_at ? $teacher->joined_at->format('d M Y') : '-' }}</span>
                            </td>
                            <td>
                                @php
                                    $referenceDate = $activeYear?->start_date ?? now();
                                @endphp
                                <span class="text-sm text-[hsl(var(--muted-foreground))]">{{ $teacher->getTenureFormatted($referenceDate) }}</span>
                            </td>
                            <td>
                                @if($teacher->is_active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary">Tidak Digaji</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('teachers.toggle-tahfidz', $teacher) }}" method="POST" class="inline-block">
                                    @csrf
                                    @if($teacher->is_tahfidz)
                                        <x-ui.button type="submit" size="sm" title="Jadikan guru biasa">
                                            <x-ui.badge variant="default">Tahfidz</x-ui.badge>
                                        </x-ui.button>
                                    @else
                                        <x-ui.button type="submit" variant="outline" size="sm" title="Jadikan guru tahfidz">
                                            <span class="text-xs text-[hsl(var(--muted-foreground))]">-</span>
                                        </x-ui.button>
                                    @endif
                                </form>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('teachers.toggle-active', $teacher) }}" method="POST" class="inline-block">
                                        @csrf
                                        @if($teacher->is_active)
                                            <x-ui.button type="submit" variant="outline" size="sm" title="Kecualikan dari gaji">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                            </x-ui.button>
                                        @else
                                            <x-ui.button type="submit" variant="outline" size="sm" title="Ikutkan dalam gaji">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </x-ui.button>
                                        @endif
                                    </form>
                                    <a href="{{ route('teachers.edit', $teacher) }}">
                                        <x-ui.button variant="outline" size="sm">Edit</x-ui.button>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus guru ini?');">
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
                                Belum ada data guru. <a href="{{ route('teachers.create') }}" class="text-[hsl(var(--primary))] hover:underline">Tambah sekarang</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
