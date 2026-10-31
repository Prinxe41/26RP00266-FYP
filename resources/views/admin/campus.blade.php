@extends('admin.dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-4">Manage Campuses</h1>

<!-- Success message -->
@if (session('success'))
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
    {{ session('success') }}
</div>
@endif


<!-- Validation Errors -->
@if ($errors->any())
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
    <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Add Campus Form -->
<form action="{{ route('admin.store-campus') }}" method="POST"
    class="mb-8 bg-white p-6 rounded shadow-md w-full md:w-1/2">
    @csrf
    <div class="mb-4">
        <label for="campus_name" class="block text-sm font-medium text-gray-700">Campus Name</label>
        <input type="text" name="campus_name" id="campus_name" required
            class="mt-1 block w-full border border-gray-300 rounded p-2 shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        Add Campus
    </button>
</form>

<!-- Campus List -->
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="bg-white p-6 rounded shadow-md">
    <h2 class="text-xl font-semibold mb-4">Existing Campuses</h2>
    <table id="campusesTable" class="display min-w-full text-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Campus Name</th>
            </tr>
        </thead>
        <tbody>
            @forelse($campuses as $index => $campus)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $campus->campus_name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center text-gray-500">No campuses found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Initialize DataTables -->
<script>
$(document).ready(function() {
    $('#campusesTable').DataTable({
        responsive: true
    });
});
</script>

</div>
@endsection