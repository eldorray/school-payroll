@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6">Edit Teacher: {{ $teacher->name }}</h2>

    <div class="bg-white p-6 rounded-lg shadow max-w-2xl">
        <form action="{{ route('teachers.update', $teacher) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Full Name
                    </label>
                    <input type="text" name="name" id="name" value="{{ $teacher->name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="position">
                        Position (Jabatan)
                    </label>
                    <input type="text" name="position" id="position" value="{{ $teacher->position }}" placeholder="e.g. Guru, Kepsek" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nip">
                        NIP
                    </label>
                    <input type="text" name="nip" id="nip" value="{{ $teacher->nip }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="joined_at">
                        Joined Date
                    </label>
                    <input type="date" name="joined_at" id="joined_at" value="{{ $teacher->joined_at ? $teacher->joined_at->format('Y-m-d') : '' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            @if($activeYear)
            @php
                $annualSetting = $activeYear ? $teacher->annualSettings->where('academic_year_id', $activeYear->id)->first() : null;
                $currentHours = $annualSetting ? $annualSetting->teaching_hours_per_month : '';
                $currentBpjs = $annualSetting ? $annualSetting->bpjs_amount : '';
            @endphp
            <hr class="my-6">
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-bold">Annual Settings & Allowances ({{ $activeYear->name }})</h3>
                <button type="button" onclick="addAllowance()" class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-2 rounded">
                    + Add Allowance
                </button>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="teaching_hours">
                    Teaching Hours (Per Month - Annual Setting)
                </label>
                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <label class="block text-xs text-gray-500 mb-1">Hours</label>
                        <input type="number" name="teaching_hours" id="teaching_hours" value="{{ $currentHours }}" placeholder="e.g. 24" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-xs text-gray-500 mb-1">BPJS Amount (Annual Setting)</label>
                        <input type="number" name="bpjs_amount" id="bpjs_amount" value="{{ $currentBpjs }}" placeholder="e.g. 50000" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">These values (Hours & BPJS) will be automatically used for monthly payroll calculations.</p>
            </div>
            
            <div id="allowances-container" class="space-y-3">
                @php $idx = 0; @endphp
                @foreach($currentAllowances as $allowance)
                    <div class="allowance-row flex gap-4 items-center">
                        <input type="text" name="allowances[{{ $idx }}][name]" value="{{ $allowance->allowance_name }}" class="shadow appearance-none border rounded flex-1 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <input type="number" name="allowances[{{ $idx }}][amount]" value="{{ $allowance->amount }}" class="shadow appearance-none border rounded w-32 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" onclick="removeAllowance(this)" class="text-red-500 hover:text-red-700 font-bold px-2" title="Remove">✕</button>
                    </div>
                    @php $idx++; @endphp
                @endforeach

                @if(count($currentAllowances) === 0)
                    <div class="allowance-row flex gap-4 items-center">
                        <input type="text" name="allowances[{{ $idx }}][name]" placeholder="Allowance Name (e.g. Wali Kelas)" class="shadow appearance-none border rounded flex-1 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <input type="number" name="allowances[{{ $idx }}][amount]" placeholder="Amount" class="shadow appearance-none border rounded w-32 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <button type="button" onclick="removeAllowance(this)" class="text-red-500 hover:text-red-700 font-bold px-2" title="Remove">✕</button>
                    </div>
                @endif
            </div>
            @endif

            <div class="flex items-center justify-between mt-8">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Update Teacher
                </button>
                <a href="{{ route('teachers.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        let allowanceIndex = {{ count($currentAllowances) > 0 ? count($currentAllowances) : 1 }};
        function addAllowance() {
            const container = document.getElementById('allowances-container');
            const div = document.createElement('div');
            div.className = 'allowance-row flex gap-4 items-center';
            div.innerHTML = `
                <input type="text" name="allowances[${allowanceIndex}][name]" placeholder="Allowance Name" class="shadow appearance-none border rounded flex-1 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <input type="number" name="allowances[${allowanceIndex}][amount]" placeholder="Amount" class="shadow appearance-none border rounded w-32 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="button" onclick="removeAllowance(this)" class="text-red-500 hover:text-red-700 font-bold px-2" title="Remove">✕</button>
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
