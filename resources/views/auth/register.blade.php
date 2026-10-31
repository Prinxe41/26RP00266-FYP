<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white p-8 rounded shadow-md w-full sm:w-[90%] md:w-[60%] lg:w-[40%]">
        <h1 class="text-2xl font-bold mb-6 text-center text-blue-700">Student Registration</h1>

        {{-- ✅ Success Message --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
            x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
            class="fixed right-4 top-20 w-[20%] bg-green-100 text-green-800 border border-green-300 rounded shadow px-4 py-3 z-50">
            <div class="flex justify-between items-start space-x-2">
                <span class="text-sm">{{ session('success') }}</span>
                <button @click="show = false" class="text-green-700 text-base hover:text-green-900">&times;</button>
            </div>
        </div>
        @endif

        {{-- ❌ Error Message --}}
        @if($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
            class="fixed right-4 top-24 w-[20%] bg-red-100 text-red-800 border border-red-300 rounded shadow px-4 py-3 z-50">
            <div class="flex justify-between items-start space-x-2">
                <ul class="text-sm list-disc pl-4 pr-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button @click="show = false" class="text-red-700 text-base hover:text-red-900 mt-1">&times;</button>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Campus --}}
            <select name="campus_id" id="campus_id" required class="w-full px-4 py-2 border rounded mb-3">
                <option value="">Select Campus</option>
                @foreach ($campuses as $campus)
                <option value="{{ $campus->campus_id }}">{{ $campus->campus_name }}</option>
                @endforeach
            </select>

            {{-- Department (Filtered) --}}
            <select name=" campus_department_id" id="campus_department_id" required
                class="w-full px-4 py-2 border rounded mb-3">
                <option value="">Select Campus First</option>
            </select>

            {{-- Faculty (Filtered) --}}
            <select name="cdo_id" id="cdo_id" required class="w-full px-4 py-2 border rounded mb-3">
                <option value="">Select Department First</option>
            </select>

            {{-- Student Info --}}
            <input name="student_reg_number" placeholder="Registration Number"
                class="w-full px-4 py-2 border rounded mb-3" required>
            <input name="student_first_name" placeholder="First Name" class="w-full px-4 py-2 border rounded mb-3"
                required>
            <input name="student_last_name" placeholder="Last Name" class="w-full px-4 py-2 border rounded mb-3"
                required>

            <select name="student_gender" class="w-full px-4 py-2 border rounded mb-3" required>
                <option value="">Select Gender</option>
                <option>Male</option>
                <option>Female</option>
            </select>

            <input type="email" name="student_email" placeholder="Email" class="w-full px-4 py-2 border rounded mb-3"
                required>
            <input name="student_phone_number" placeholder="Phone Number" class="w-full px-4 py-2 border rounded mb-3"
                required>
            <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border rounded mb-3"
                required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password"
                class="w-full px-4 py-2 border rounded mb-3" required>

            <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
                Register
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-blue-600">Already have an account? Login</a>
            </div>
        </form>
    </div>

    {{-- AJAX Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $('#campus_id').on('change', function() {
        let campusId = $(this).val();
        $('#campus_department_id').html('<option>Loading...</option>');
        $('#cdo_id').html('<option>Select Department First</option>');

        $.get('/get-campus-departments/' + campusId, function(data) {
            let options = '<option value="">Select Department</option>';
            data.forEach(dept => {
                options +=
                    `<option value="${dept.campus_department_id}">${dept.department_name}</option>`;
            });
            $('#campus_department_id').html(options);
        }).fail(function() {
            $('#campus_department_id').html('<option>Error loading departments</option>');
        });
    });

    $('#campus_department_id').on('change', function() {
        let campusDepartmentId = $(this).val();
        $('#cdo_id').html('<option>Loading...</option>');

        $.get('/get-faculties-by-campus-department/' + campusDepartmentId, function(data) {
            let options = '<option value="">Select Faculty</option>';
            data.forEach(item => {
                options += `<option value="${item.cdo_id}">${item.faculty_name}</option>`;
            });
            $('#cdo_id').html(options);
        }).fail(function() {
            $('#cdo_id').html('<option>Error loading faculties</option>');
        });
    });
    </script>

</body>

</html>