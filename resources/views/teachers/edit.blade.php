@extends('layouts.app')

@section('title', 'Edit Guru: ' . $teacher->name)

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[hsl(var(--foreground))]">Edit Guru</h1>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $teacher->name }}</p>
    </div>

    <x-ui.card class="max-w-2xl">
        <form action="{{ route('teachers.update', $teacher) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.input 
                    name="name" 
                    label="Nama Lengkap" 
                    :value="$teacher->name"
                    placeholder="Masukkan nama lengkap"
                    :error="$errors->first('name')"
                    required 
                />
                <x-ui.input 
                    name="position" 
                    label="Jabatan" 
                    :value="$teacher->position"
                    placeholder="cth: Guru, Kepsek"
                    :error="$errors->first('position')"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.input 
                    name="nip" 
                    label="NIP" 
                    :value="$teacher->nip"
                    placeholder="Nomor Induk Pegawai"
                    :error="$errors->first('nip')"
                />
                <x-ui.input 
                    type="date"
                    name="joined_at" 
                    label="Tanggal Bergabung"
                    :value="$teacher->joined_at ? $teacher->joined_at->format('Y-m-d') : ''"
                    :error="$errors->first('joined_at')"
                />
            </div>

            @if($activeYear)
            @php
                $annualSetting = $activeYear ? $teacher->annualSettings->where('academic_year_id', $activeYear->id)->first() : null;
                $currentHours = $annualSetting ? $annualSetting->teaching_hours_per_month : '';
                $currentBpjs = $annualSetting ? $annualSetting->bpjs_amount : '';
            @endphp
            
            <hr class="my-6 border-[hsl(var(--border))]">
            
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">Pengaturan & Tunjangan ({{ $activeYear->name }})</h3>
                <x-ui.button type="button" variant="outline" size="sm" onclick="addAllowance()">
                    + Tambah Tunjangan
                </x-ui.button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <x-ui.input 
                    type="number"
                    name="teaching_hours" 
                    label="Jam Mengajar (Per Bulan)"
                    :value="$currentHours"
                    placeholder="cth: 24"
                    :error="$errors->first('teaching_hours')"
                />
                <x-ui.input 
                    type="number"
                    name="bpjs_amount" 
                    label="Potongan BPJS"
                    :value="$currentBpjs"
                    placeholder="cth: 50000"
                    :error="$errors->first('bpjs_amount')"
                />
            </div>
            <p class="text-xs text-[hsl(var(--muted-foreground))] mb-6">Nilai jam mengajar & BPJS akan otomatis digunakan untuk kalkulasi gaji bulanan.</p>
            
            <div id="allowances-container" class="space-y-3 mb-6">
                @php $idx = 0; @endphp
                @foreach($currentAllowances as $allowance)
                    <div class="allowance-row flex gap-4 items-center">
                        <input type="text" name="allowances[{{ $idx }}][name]" value="{{ $allowance->allowance_name }}" class="input flex-1">
                        <input type="number" name="allowances[{{ $idx }}][amount]" value="{{ $allowance->amount }}" class="input w-32">
                        <button type="button" onclick="removeAllowance(this)" class="btn btn-ghost text-[hsl(var(--destructive))] px-2" title="Hapus">✕</button>
                    </div>
                    @php $idx++; @endphp
                @endforeach

                @if(count($currentAllowances) === 0)
                    <div class="allowance-row flex gap-4 items-center">
                        <input type="text" name="allowances[{{ $idx }}][name]" placeholder="Nama Tunjangan (cth: Wali Kelas)" class="input flex-1">
                        <input type="number" name="allowances[{{ $idx }}][amount]" placeholder="Jumlah" class="input w-32">
                        <button type="button" onclick="removeAllowance(this)" class="btn btn-ghost text-[hsl(var(--destructive))] px-2" title="Hapus">✕</button>
                    </div>
                @endif
            </div>
            @endif

            <div class="flex items-center justify-between pt-4 border-t border-[hsl(var(--border))]">
                <x-ui.button type="submit">
                    Perbarui Guru
                </x-ui.button>
                <a href="{{ route('teachers.index') }}" class="text-sm text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">
                    Batal
                </a>
            </div>
        </form>
    </x-ui.card>

    <script>
        let allowanceIndex = {{ count($currentAllowances) > 0 ? count($currentAllowances) : 1 }};
        function addAllowance() {
            const container = document.getElementById('allowances-container');
            const div = document.createElement('div');
            div.className = 'allowance-row flex gap-4 items-center';
            div.innerHTML = `
                <input type="text" name="allowances[${allowanceIndex}][name]" placeholder="Nama Tunjangan" class="input flex-1">
                <input type="number" name="allowances[${allowanceIndex}][amount]" placeholder="Jumlah" class="input w-32">
                <button type="button" onclick="removeAllowance(this)" class="btn btn-ghost text-[hsl(var(--destructive))] px-2" title="Hapus">✕</button>
            `;
            container.appendChild(div);
            allowanceIndex++;
        }
        
        function removeAllowance(btn) {
            const row = btn.closest('.allowance-row');
            if (row) row.remove();
        }
    </script>
@endsection
