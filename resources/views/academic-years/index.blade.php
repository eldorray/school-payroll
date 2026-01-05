@extends('layouts.app')

@section('title', 'Tahun Ajaran')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Tahun Ajaran</h1>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Kelola tahun ajaran dan pengaturan tarif gaji</p>
        </div>
        <a href="{{ route('academic-years.create') }}">
            <x-ui.button>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Tahun Ajaran
            </x-ui.button>
        </a>
    </div>

    <x-ui.card>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($years as $year)
                        <tr>
                            <td>
                                <div class="font-medium text-[hsl(var(--foreground))]">{{ $year->name }}</div>
                            </td>
                            <td>
                                <span class="text-sm text-[hsl(var(--muted-foreground))]">{{ $year->start_date->format('d M Y') }}</span>
                            </td>
                            <td>
                                <span class="text-sm text-[hsl(var(--muted-foreground))]">{{ $year->end_date->format('d M Y') }}</span>
                            </td>
                            <td class="text-center">
                                @if($year->is_active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary">Tidak Aktif</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('academic-years.edit', $year) }}">
                                        <x-ui.button variant="outline" size="sm">Edit</x-ui.button>
                                    </a>
                                    
                                    @if(!$year->is_active)
                                        <form action="{{ route('academic-years.activate', $year) }}" method="POST" class="inline-block">
                                            @csrf
                                            <x-ui.button type="submit" variant="secondary" size="sm" class="text-green-600">
                                                Aktifkan
                                            </x-ui.button>
                                        </form>
                                    @endif

                                    <form action="{{ route('academic-years.destroy', $year) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="destructive" size="sm">Hapus</x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-[hsl(var(--muted-foreground))]">
                                Belum ada data tahun ajaran. <a href="{{ route('academic-years.create') }}" class="text-[hsl(var(--primary))] hover:underline">Tambah sekarang</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
