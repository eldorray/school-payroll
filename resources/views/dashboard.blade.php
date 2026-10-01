@extends('layouts.app')
@section('title', 'Ringkasan')
@section('content')
    <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
        <div>
            <h1>Ringkasan</h1>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $period->locale('id')->translatedFormat('F Y') }} · Tahun ajaran {{ $activeYear?->name ?? 'belum diatur' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('dashboard') }}" method="GET" class="flex gap-2 items-center">
                <label for="summary-month" class="sr-only">Bulan ringkasan</label>
                <select id="summary-month" name="month" class="input w-32">
                    @for($month = 1; $month <= 12; $month++)
                        <option value="{{ $month }}" @selected($month === $period->month)>{{ $period->copy()->month($month)->locale('id')->translatedFormat('F') }}</option>
                    @endfor
                </select>
                <label for="summary-year" class="sr-only">Tahun ringkasan</label>
                <input id="summary-year" name="year" type="number" min="2000" max="2100" value="{{ $period->year }}" class="input w-24">
                <button type="submit" class="btn btn-outline">Tampilkan</button>
            </form>
            <a href="{{ route('payrolls.create') }}" class="btn btn-primary"><span aria-hidden="true" class="text-xl">+</span> Batch baru</a>
        </div>
    </div>

    <div class="stat-strip mb-6">
        <div><p class="text-[hsl(var(--muted-foreground))]">Total dibayar</p><p class="money">{{ number_format($totalPaid, 0, ',', '.') }}</p><p class="text-xs text-[hsl(var(--muted-foreground))]">Honor guru, tahfidz, dan ekskul (Rp)</p></div>
        <div><p class="text-[hsl(var(--muted-foreground))]">Penerima</p><p class="money">{{ $recipientCount }}</p><p class="text-xs text-[hsl(var(--muted-foreground))]">{{ $payrolls->count() }} slip honor · {{ $extras->count() }} pembayaran ekskul</p></div>
        <div><p class="text-[hsl(var(--muted-foreground))]">Rata-rata honor guru</p><p class="money">{{ number_format($payrolls->avg('total_salary') ?? 0, 0, ',', '.') }}</p><p class="text-xs text-[hsl(var(--muted-foreground))]">Per slip, setelah potongan (Rp)</p></div>
        <div><p class="text-[hsl(var(--muted-foreground))]">Potongan</p><p class="money">{{ number_format($deductions, 0, ',', '.') }}</p><p class="text-xs text-amber-800">BPJS, insentif, terlambat, dan lainnya (Rp)</p></div>
    </div>

    <x-ui.card class="mb-6">
        <x-slot:header><h2 class="text-lg">Penggajian {{ $period->locale('id')->translatedFormat('F Y') }}</h2></x-slot:header>
        <div class="grid sm:grid-cols-3 gap-6">
            @foreach([
                ['Data guru', $teacherCount . ' guru aktif', 'teachers.index', $teacherCount > 0],
                ['Honor guru & tahfidz', $payrolls->count() . ' slip tersimpan', 'payrolls.index', $payrolls->isNotEmpty()],
                ['Gaji ekskul', $extras->count() . ' pembayaran tersimpan', 'extracurricular-payrolls.index', $extras->isNotEmpty()],
            ] as [$label, $detail, $route, $ready])
                <a href="{{ route($route, ['month' => $period->month, 'year' => $period->year]) }}" class="block group">
                    <div class="h-1 rounded-full mb-3 {{ $ready ? 'bg-emerald-700' : 'bg-amber-400' }}"></div>
                    <p class="font-semibold group-hover:underline">{{ $label }} <span aria-hidden="true">→</span></p>
                    <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $detail }}</p>
                </a>
            @endforeach
        </div>
    </x-ui.card>

    <div class="grid xl:grid-cols-[minmax(0,2fr)_minmax(300px,1fr)] gap-6">
        <x-ui.card class="min-w-0">
            <x-slot:header><div class="flex justify-between items-center gap-3"><h2 class="text-lg">Batch terbaru</h2><a href="{{ route('payrolls.index') }}" class="text-sm text-slate-600 underline underline-offset-4">Semua batch</a></div></x-slot:header>
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Batch</th><th class="text-right">Penerima</th><th class="text-right">Total (Rp)</th><th>Dibuat</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td><p class="font-semibold">{{ $batch->display_name }}</p><p class="text-xs text-[hsl(var(--muted-foreground))]">{{ $batch->is_tahfidz ? 'Tahfidz' : 'Honor Guru' }} · {{ $batch->period }}</p></td>
                                <td class="text-right">{{ $batch->payrolls_count }}</td>
                                <td class="text-right font-semibold whitespace-nowrap">{{ number_format($batch->payrolls_sum_total_salary ?? 0, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap text-[hsl(var(--muted-foreground))]">{{ $batch->created_at->format('d M Y') }}</td>
                                <td class="text-right"><a href="{{ route($batch->is_tahfidz ? 'tahfidz-payrolls.batch.edit' : 'payrolls.batch.edit', $batch) }}" class="btn btn-outline btn-sm">Edit</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-12 text-center text-[hsl(var(--muted-foreground))]">Belum ada batch penggajian. <a href="{{ route('payrolls.create') }}" class="underline text-slate-700">Buat batch pertama</a></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
        <x-ui.card>
            <h2 class="text-lg">Komposisi {{ $period->locale('id')->translatedFormat('F') }}</h2>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Bruto {{ number_format($composition->sum(), 0, ',', '.') }}, sebelum potongan</p>
            @php $colors = ['#0e1a2b', '#3d587b', '#91a7c4', '#f5aa35']; @endphp
            <div class="flex h-4 rounded-md overflow-hidden my-5 bg-gray-100" aria-hidden="true">
                @foreach($composition as $amount)
                    <div style="width: {{ $composition->sum() > 0 ? $amount / $composition->sum() * 100 : 0 }}%; background: {{ $colors[$loop->index] }}"></div>
                @endforeach
            </div>
            <dl>
                @foreach($composition as $label => $amount)
                    <div class="flex items-center gap-3 py-4 border-b">
                        <span class="w-3 h-3 rounded-sm shrink-0" style="background: {{ $colors[$loop->index] }}" aria-hidden="true"></span>
                        <dt class="flex-1">{{ $label }}</dt><dd class="money font-semibold">{{ number_format($amount, 0, ',', '.') }}</dd>
                    </div>
                @endforeach
            </dl>
            <a href="{{ route('academic-years.index') }}" class="flex items-center justify-between gap-3 bg-gray-50 rounded-lg p-4 mt-6 text-sm"><span class="text-[hsl(var(--muted-foreground))]">Tahun ajaran aktif</span><span class="font-semibold">{{ $activeYear?->name ?? 'Atur sekarang →' }}</span></a>
        </x-ui.card>
    </div>
@endsection
