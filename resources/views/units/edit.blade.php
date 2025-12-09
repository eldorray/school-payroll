@extends('layouts.app')

@section('content')
    <h1 class="page-title">Unit Settings</h1>

    <div class="glass-card p-6" style="max-width: 600px;">
        <form method="POST" action="{{ route('units.update') }}">
            @csrf
            @method('PATCH')

            <!-- Unit Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Nama Unit / Sekolah</label>
                <input type="text" id="name" name="name" value="{{ old('name', $unit->name) }}" 
                       class="input-modern" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div class="mb-4">
                <label for="location" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Tempat (Lokasi)</label>
                <input type="text" id="location" name="location" value="{{ old('location', $unit->location) }}" 
                       class="input-modern" placeholder="e.g., Cirebon" required>
                @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Signature Date -->
            <div class="mb-4">
                <label for="signature_date" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Tanggal Tanda Tangan</label>
                <input type="date" id="signature_date" name="signature_date" 
                       value="{{ old('signature_date', $unit->signature_date ? $unit->signature_date->format('Y-m-d') : '') }}" 
                       class="input-modern" required>
                @error('signature_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Principal Name -->
            <div class="mb-6">
                <label for="principal_name" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">Nama Kepala Sekolah</label>
                <input type="text" id="principal_name" name="principal_name" value="{{ old('principal_name', $unit->principal_name) }}" 
                       class="input-modern" placeholder="e.g., Drs. H. Ahmad Suryadi, M.Pd" required>
                @error('principal_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Changes
                </button>
                <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Preview -->
    <div class="glass-card p-6 mt-6" style="max-width: 600px;">
        <h3 class="text-lg font-semibold mb-4" style="color: var(--text-primary);">Preview Tanda Tangan</h3>
        <div style="text-align: right; font-size: 14px;">
            <p style="margin-bottom: 5px;">{{ $unit->location ?? 'Cirebon' }}, {{ $unit->signature_date ? $unit->signature_date->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y') }}</p>
            <p style="margin-bottom: 60px;">Kepala Sekolah</p>
            <p style="text-decoration: underline; font-weight: bold;">{{ $unit->principal_name ?? '________________________' }}</p>
        </div>
    </div>
@endsection
