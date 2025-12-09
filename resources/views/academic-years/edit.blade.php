@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-6">Edit Academic Year</h2>

    <div class="bg-white p-6 rounded-lg shadow max-w-2xl">
        <form action="{{ route('academic-years.update', $academicYear) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Name
                </label>
                <input type="text" name="name" id="name" value="{{ $academicYear->name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="start_date">
                        Start Date
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ $academicYear->start_date->format('Y-m-d') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="end_date">
                        End Date
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ $academicYear->end_date->format('Y-m-d') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
            </div>

            <hr class="my-6">
            <h3 class="text-lg font-bold mb-4">Payroll Settings</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="teaching_rate">
                        Teaching Rate / Hour
                    </label>
                    <input type="number" name="teaching_rate" id="teaching_rate" value="{{ $academicYear->payrollSettings->teaching_rate_per_hour ?? 0 }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="transport_rate">
                        Transport Rate / Visit
                    </label>
                    <input type="number" name="transport_rate" id="transport_rate" value="{{ $academicYear->payrollSettings->transport_rate_per_visit ?? 0 }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="masa_kerja_rate">
                        Masa Kerja Rate / Year
                    </label>
                    <input type="number" name="masa_kerja_rate" id="masa_kerja_rate" value="{{ $academicYear->payrollSettings->masa_kerja_rate_per_year ?? 0 }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Update Academic Year
                </button>
                <a href="{{ route('academic-years.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
