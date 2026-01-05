@extends('layouts.app')

@section('title', 'Tambah Ekskul')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Tambah Ekstrakurikuler</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Tambahkan data ekskul baru</p>
    </div>

    <x-ui.card class="max-w-xl">
        <form action="{{ route('extracurriculars.store') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Nama Ekskul</label>
                    <x-ui.input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Paskibra, Pramuka, Hadroh" required />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Tarif per Volume</label>
                    <x-ui.input type="number" name="rate" value="{{ old('rate', 0) }}" min="0" step="1000" required />
                    <p class="text-xs text-[hsl(var(--muted-foreground))]">Tarif yang akan dikalikan dengan volume (jumlah pertemuan)</p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-[hsl(var(--border))]">
                <x-ui.button type="submit">Simpan</x-ui.button>
                <a href="{{ route('extracurriculars.index') }}">
                    <x-ui.button type="button" variant="outline">Batal</x-ui.button>
                </a>
            </div>
        </form>
    </x-ui.card>
@endsection
