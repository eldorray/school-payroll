@extends('layouts.app')

@section('title', 'Input Gaji Ekskul')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Input Penggajian Ekskul</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Tambahkan data honor pembina ekstrakurikuler</p>
    </div>

    @if($extracurriculars->isEmpty())
        <x-ui.alert type="warning" class="mb-6">
            <strong>Peringatan:</strong> Belum ada data ekskul. <a href="{{ route('extracurriculars.create') }}" class="underline">Tambah ekskul dulu</a>.
        </x-ui.alert>
    @endif

    <x-ui.card>
        <div class="mb-6 p-4 rounded-lg bg-[hsl(var(--secondary))]">
            <h3 class="font-semibold text-[hsl(var(--foreground))]">Tahun Ajaran: <span class="text-[hsl(var(--primary))]">{{ $activeYear->name }}</span></h3>
        </div>

        <form action="{{ route('extracurricular-payrolls.store') }}" method="POST" x-data="ekskul()">
            @csrf
            
            <div class="flex flex-wrap gap-4 mb-6">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Bulan</label>
                    <select name="month" class="input w-40">
                        @php
                            $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-[hsl(var(--foreground))]">Tahun</label>
                    <input type="number" name="year" value="{{ $currentYear }}" class="input w-24">
                </div>
            </div>

            <!-- Dynamic Entries -->
            <div class="space-y-3 mb-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-[hsl(var(--foreground))]">Data Pembina Ekskul</h3>
                    <x-ui.button type="button" variant="outline" size="sm" @click="addEntry()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Baris
                    </x-ui.button>
                </div>

                <template x-for="(entry, index) in entries" :key="index">
                    <div class="flex flex-wrap items-end gap-3 p-4 rounded-lg border border-[hsl(var(--border))] bg-[hsl(var(--card))]">
                        <div class="flex-1 min-w-[200px] space-y-2">
                            <label class="text-sm font-medium text-[hsl(var(--foreground))]">Guru</label>
                            <select :name="'entries[' + index + '][teacher_id]'" class="input" required>
                                <option value="">Pilih Guru</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px] space-y-2">
                            <label class="text-sm font-medium text-[hsl(var(--foreground))]">Ekskul</label>
                            <select :name="'entries[' + index + '][extracurricular_id]'" class="input" required @change="updateRate($event, index)">
                                <option value="">Pilih Ekskul</option>
                                @foreach($extracurriculars as $ekskul)
                                    <option value="{{ $ekskul->id }}" data-rate="{{ $ekskul->rate }}">{{ $ekskul->name }} (Rp {{ number_format($ekskul->rate, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-24 space-y-2">
                            <label class="text-sm font-medium text-[hsl(var(--foreground))]">Volume</label>
                            <input type="number" :name="'entries[' + index + '][volume]'" x-model.number="entry.volume" class="input text-center" min="0" required>
                        </div>
                        <div class="w-32 space-y-2">
                            <label class="text-sm font-medium text-[hsl(var(--foreground))]">Jumlah</label>
                            <div class="input bg-[hsl(var(--secondary))] text-right font-semibold text-green-600" x-text="formatRupiah(entry.rate * entry.volume)"></div>
                        </div>
                        <x-ui.button type="button" variant="destructive" size="sm" @click="removeEntry(index)" x-show="entries.length > 1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </x-ui.button>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-[hsl(var(--border))]">
                <div class="text-lg font-semibold">
                    Total: <span class="text-green-600" x-text="formatRupiah(totalAmount())"></span>
                </div>
                <x-ui.button type="submit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <script>
        function ekskul() {
            return {
                entries: [{ rate: 0, volume: 0 }],
                addEntry() {
                    this.entries.push({ rate: 0, volume: 0 });
                },
                removeEntry(index) {
                    this.entries.splice(index, 1);
                },
                updateRate(event, index) {
                    const option = event.target.selectedOptions[0];
                    this.entries[index].rate = parseFloat(option.dataset.rate) || 0;
                },
                totalAmount() {
                    return this.entries.reduce((sum, e) => sum + (e.rate * e.volume), 0);
                },
                formatRupiah(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                }
            }
        }
    </script>
@endsection
