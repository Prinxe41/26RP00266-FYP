@extends('admin.dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-4">Manage Faculties</h1>

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

{{-- Add Faculty (Option) Form --}}
<form action="{{ route('admin.store-campus-faculty') }}" method="POST"
    class="mb-8 bg-white p-6 rounded shadow-md w-full md:w-1/2">
    @csrf

    {{-- Select Department --}}
    <div class="mb-4">
        <label for="campus_department_id" class="block text-sm font-medium text-gray-700">Select Department</label>
        <select name="campus_department_id" id="campus_department_id" required
            class="mt-1 block w-full border border-gray-300 rounded p-2">
            <option value="">-- Choose Department --</option>
            @foreach ($campusDepartments as $cd)
            <option value="{{ $cd->campus_department_id }}" data-dept="{{ $cd->department_code }}">
                {{ $cd->department_name }} @ {{ $cd->campus_name }}
            </option>

            @endforeach
        </select>
    </div>

    {{-- Select Faculty (Filtered via AJAX) --}}
    <div class="mb-4">
        <label for="faculty_code" class="block text-sm font-medium text-gray-700">Select Faculty (Option)</label>
        <select name="faculty_code" id="faculty_code" required
            class="mt-1 block w-full border border-gray-300 rounded p-2">
            <option value="">-- Select a department first --</option>
        </select>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        Add Faculty
    </button>
</form>

{{-- AJAX Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#campus_department_id').on('change', function() {
    let deptCode = $(this).find(':selected').data('dept');
    console.log("Selected deptCode: ", deptCode); // ✅ See this in console

    if (!deptCode) {
        $('#faculty_code').html('<option>-- No department code found --</option>');
        return;
    }

    $('#faculty_code').html('<option>Loading options...</option>');

    $.ajax({
        url: '/admin/get-faculties/' + deptCode,
        method: 'GET',
        success: function(data) {
            let options = '<option value="">-- Choose Faculty --</option>';
            data.forEach(faculty => {
                options +=
                    `<option value="${faculty.faculty_code}">${faculty.faculty_name}</option>`;
            });
            $('#faculty_code').html(options);
        },
        error: function(xhr) {
            console.error("AJAX error:", xhr.responseText); // ✅ See real error
            $('#faculty_code').html('<option>Error loading faculties</option>');
        }
    });
});
</script>

{{-- Show Inserted Faculty Options --}}
<h2 class="text-xl font-semibold mb-2">Assigned Faculties</h2>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-white p-8 rounded-lg shadow-md">
    <!-- <h2 class="text-2xl font-semibold mb-6 text-gray-800">Assigned Faculties</h2> -->

    <div class="overflow-x-auto">
        <table id="facultyTable" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Campus</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Faculty Name</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($assignedFaculties as $item)
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $item->department_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $item->campus_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-900">{{ $item->faculty_name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#facultyTable').DataTable({
        responsive: true,
        language: {
            search: "Filter:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                next: "Next →",
                previous: "← Prev"
            }
        },
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ], // Optional: Customize page length options
        "columnDefs": [{
                "className": "text-left",
                "targets": "_all"
            } // Align all columns to the left
        ]
    });
});
</script>


@endsection