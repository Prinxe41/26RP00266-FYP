@extends('admin.dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-4">Manage HOD</h1>

{{-- Success Message --}}
@if (session('success'))
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
    {{ session('success') }}
</div>
@endif

{{-- Validation Errors --}}
@if ($errors->any())
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
    <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Assign HOD Form --}}
<form action="{{ route('admin.store-hod') }}" method="POST" class="mb-8 bg-white p-6 rounded shadow-md w-full md:w-2/3">
    @csrf

    {{-- Select Campus --}}
    <div class="mb-4">
        <label for="campus_id" class="block text-sm font-medium text-gray-700">Select Campus</label>
        <select name="campus_id" id="campus_id" required class="mt-1 block w-full border border-gray-300 rounded p-2">
            <option value="">-- Choose Campus --</option>
            @foreach ($campuses as $campus)
            <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Select Department (filtered by campus_id) --}}
    <div class="mb-4">
        <label for="department_code" class="block text-sm font-medium text-gray-700">Select Department</label>
        <select name="department_code" id="department_code" required
            class="mt-1 block w-full border border-gray-300 rounded p-2">
            <option value="">-- Select a campus first --</option>
        </select>
    </div>

    {{-- HOD Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" name="first_name" id="first_name" required
                class="mt-1 block w-full border border-gray-300 rounded p-2">
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" name="last_name" id="last_name" required
                class="mt-1 block w-full border border-gray-300 rounded p-2">
        </div>
        <div>
            <label for="hod_email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="hod_email" id="hod_email" required
                class="mt-1 block w-full border border-gray-300 rounded p-2">
        </div>
        <div>
            <label for="hod_phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input type="text" name="hod_phone" id="hod_phone" required
                class="mt-1 block w-full border border-gray-300 rounded p-2">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password" required
                class="mt-1 block w-full border border-gray-300 rounded p-2">
        </div>
    </div>

    <button type="submit" class="mt-6 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        Assign HOD
    </button>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#campus_id').on('change', function() {
    let campusId = $(this).val();
    $('#department_code').html('<option>Loading...</option>');

    $.ajax({
        url: '/admin/get-departments-by-campus/' + campusId,
        method: 'GET',
        success: function(data) {
            let options = '<option value="">-- Choose Department --</option>';
            data.forEach(item => {
                options +=
                    `<option value="${item.department_code}">${item.department_name}</option>`;
            });
            $('#department_code').html(options);
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            $('#department_code').html('<option>Error loading departments</option>');
        }
    });
});
</script>


{{-- Assigned HODs Table --}}
<h2 class="text-xl font-semibold mb-4">Assigned HODs</h2>
<table id="hodsTable" class="min-w-full bg-white shadow-md rounded overflow-hidden">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-4 py-2">HOD Name</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Phone</th>
            <th class="px-4 py-2">Campus</th>
            <th class="px-4 py-2">Department</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($hods as $hod)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $hod->first_name }} {{ $hod->last_name }}</td>
            <td class="px-4 py-2">{{ $hod->hod_email }}</td>
            <td class="px-4 py-2">{{ $hod->hod_phone }}</td>
            <td class="px-4 py-2">{{ $hod->campus_name }}</td>
            <td class="px-4 py-2">{{ $hod->department_name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- DataTables JS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#hodsTable').DataTable({
        pageLength: 5
    });
});
</script>
@endsection