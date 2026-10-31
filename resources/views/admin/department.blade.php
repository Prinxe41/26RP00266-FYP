@extends('admin.dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-4">Manage Departments</h1>

{{-- Display Success Message --}}
@if (session('success'))
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
    {{ session('success') }}
</div>
@endif

{{-- Display Validation Errors --}}
@if ($errors->any())
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
    <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Add Department to Campus Form --}}
<form action="{{ route('admin.store-campus-department') }}" method="POST"
    class="mb-8 bg-white p-6 rounded shadow-md w-full md:w-1/2">
    @csrf

    {{-- Campus Dropdown --}}
    <div class="mb-4">
        <label for="campus_id" class="block text-sm font-medium text-gray-700">Select Campus</label>
        <select name="campus_id" id="campus_id" required class="mt-1 block w-full border border-gray-300 rounded p-2">
            <option value="">-- Choose Campus --</option>
            @foreach ($campuses as $campus)
            <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Department Dropdown --}}
    <div class="mb-4">
        <label for="department_code" class="block text-sm font-medium text-gray-700">Select Department</label>
        <select name="department_code" id="department_code" required
            class="mt-1 block w-full border border-gray-300 rounded p-2">
            <option value="">-- Choose Department --</option>
            @foreach ($departments as $department)
            <option value="{{ $department->department_code }}">{{ $department->department_name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Submit Button --}}
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        Add Department
    </button>
</form>
@endsection