@extends('layouts.app')

@section('title', 'Edit Ekskul')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Edit Ekstrakurikuler</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $extracurricular->name }}</p>
    </div>

    <x-ui.card class="max-w-xl">
        <form action="{{ route('extracurriculars.update', $extracurricular) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Nama Ekskul</label>
                    <x-ui.input type="text" name="name" value="{{ old('name', $extracurricular->name) }}" required />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Tarif per Volume</label>
                    <x-ui.input type="number" name="rate" value="{{ old('rate', $extracurricular->rate) }}" min="0" step="1000" required />
                    <p class="text-xs text-[hsl(var(--muted-foreground))]">Tarif yang akan dikalikan dengan volume</p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-[hsl(var(--border))]">
                <x-ui.button type="submit">Update</x-ui.button>
                <a href="{{ route('extracurriculars.index') }}">
                    <x-ui.button type="button" variant="outline">Batal</x-ui.button>
                </a>
            </div>
        </form>
    </x-ui.card>
@endsection
