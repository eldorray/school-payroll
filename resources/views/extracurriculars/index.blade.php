@extends('layouts.app')

@section('title', 'Data Ekskul')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Data Ekstrakurikuler</h1>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Kelola data ekskul dan tarif honor per volume</p>
        </div>
        <a href="{{ route('extracurriculars.create') }}">
            <x-ui.button>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Ekskul
            </x-ui.button>
        </a>
    </div>

    <x-ui.card>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Ekskul</th>
                        <th class="text-right">Tarif per Volume</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($extracurriculars as $ekskul)
                        <tr>
                            <td>
                                <div class="font-medium text-[hsl(var(--foreground))]">{{ $ekskul->name }}</div>
                            </td>
                            <td class="text-right">
                                <span class="font-medium">Rp {{ number_format($ekskul->rate, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('extracurriculars.edit', $ekskul) }}">
                                        <x-ui.button variant="outline" size="sm">Edit</x-ui.button>
                                    </a>
                                    <form action="{{ route('extracurriculars.destroy', $ekskul) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus ekskul ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="destructive" size="sm">Hapus</x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-8 text-[hsl(var(--muted-foreground))]">
                                Belum ada data ekskul. <a href="{{ route('extracurriculars.create') }}" class="text-[hsl(var(--primary))] hover:underline">Tambah sekarang</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
