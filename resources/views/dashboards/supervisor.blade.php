<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <nav class="bg-purple-700 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">RP-FYP Supervisor Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span>Welcome, {{ $supervisor->supervisor_first_name }} {{ $supervisor->supervisor_last_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="bg-purple-800 px-4 py-2 rounded hover:bg-purple-900">Logout</button>
                </form>
            </div>
        </div>
    </nav>

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

    <main class="container mx-auto py-8 px-4 flex-grow">
        <!-- Supervisor Info -->
        <div class="bg-white rounded shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Supervisor Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Name</p>
                    <p class="font-semibold">{{ $supervisor->supervisor_first_name }}
                        {{ $supervisor->supervisor_last_name }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Email</p>
                    <p class="font-semibold">{{ $supervisor->supervisor_email }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Phone</p>
                    <p class="font-semibold">{{ $supervisor->supervisor_phone_number }}</p>
                </div>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- My Students Table -->
            <div class="bg-white rounded shadow-md p-6">
                <h3 class="text-xl font-bold mb-4">My Students</h3>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="text-left p-3 border">Reg Number</th>
                            <th class="text-left p-3 border">Name</th>
                            <th class="text-left p-3 border">Project</th>
                            <th class="text-left p-3 border">Status</th>
                            <th class="text-left p-3 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border">{{ $student->student_reg_number }}</td>
                            <td class="p-3 border">{{ $student->student_first_name }} {{ $student->student_last_name }}
                            </td>
                            <td class="p-3 border">{{ $student->project_name ?? 'No Project Yet' }}</td>
                            <td class="p-3 border">@if ($student->approved === null)
                                <span class="text-yellow-600 font-semibold">Pending</span>
                                @elseif ($student->approved)
                                <span class="text-green-600 font-semibold">Approved</span>
                                @else
                                <span class="text-red-600 font-semibold">Rejected</span><br>
                                @endif
                            </td>

                            <td class="p-3 border">
                                <button
                                    onclick="document.getElementById('modal-{{ $student->project_code }}').classList.remove('hidden')"
                                    class="text-blue-600 hover:underline mr-2">
                                    View
                                </button>
                                <a href="mailto:{{ $student->student_email }}"
                                    class=" text-purple-600 hover:underline mr-2">Email</a>
                                <a href="{{ route('chat.index', ['project_code' => $student->project_code]) }}"
                                    class=" bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 flex
                                                                                                                                items-center space-x-1 inline-flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>Chat</span>
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-3 text-center text-gray-500">No students assigned yet.</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
                @foreach ($students as $student)
                <!-- Modal -->
                <div id="modal-{{ $student->project_code }}"
                    class=" fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                    <div class="bg-white rounded-lg w-full max-w-2xl p-6 relative">
                        <!-- Close Button -->
                        <button
                            onclick="document.getElementById('modal-{{ $student->project_code }}').classList.add('hidden')"
                            class="absolute top-2 right-2 text-gray-500 hover:text-black">
                            ✕
                        </button>

                        <h2 class="text-xl font-bold mb-2">{{ $student->project_name }}</h2>
                        <p class="text-sm text-gray-700 mb-1"><strong>Student:</strong>
                            {{ $student->student_first_name }} {{ $student->student_last_name }}</p>
                        <p class="text-sm text-gray-700 mb-1"><strong>Reg No:</strong>
                            {{ $student->student_reg_number }}</p>
                        <p class="text-sm text-gray-700 mb-2"><strong>Email:</strong> {{ $student->student_email }}</p>

                        <p class="text-sm mb-2"><strong>Problems:</strong><br>{{ $student->project_problems }}</p>
                        <p class="text-sm mb-2"><strong>Solutions:</strong><br>{{ $student->project_solutions }}</p>
                        <p class="text-sm mb-2"><strong>Abstract:</strong><br>{{ $student->project_abstract }}</p>

                        @if ($student->project_dissertation)
                        <p class="text-sm"><strong>Dissertation:</strong>
                            <a href="{{ asset('storage/' . $student->project_dissertation) }}" target=" _blank"
                                class="text-blue-600 hover:underline">
                                View PDF
                            </a>
                        </p>
                        @endif

                        @if ($student->project_source_codes)
                        <p class="text-sm mt-1"><strong>Source Code:</strong>
                            <a href="{{ asset('storage/' . $student->project_source_codes) }}" target=" _blank"
                                class="text-blue-600 hover:underline">
                                Download ZIP
                            </a>
                        </p>
                        @endif
                    </div>
                </div>
                @endforeach

            </div>

            <!-- Pending Reviews Table -->
            <div class="bg-white rounded shadow-md p-6">
                <h3 class="text-xl font-bold mb-4">Pending Reviews</h3>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="text-left p-3 border">Date</th>
                            <th class="text-left p-3 border">Student</th>
                            <th class="text-left p-3 border">Project</th>
                            <th class="text-left p-3 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border">2025-04-15</td>
                            <td class="p-3 border">Alice Niyonsaba</td>
                            <td class="p-3 border">Smart Agriculture</td>
                            <td class="p-3 border">
                                <a href="#" class="text-blue-600 hover:underline mr-2">Review</a>
                                <a href="#" class="text-red-600 hover:underline">Reject</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border">2025-04-17</td>
                            <td class="p-3 border">Eric Nshimiyimana</td>
                            <td class="p-3 border">Drone-based Delivery</td>
                            <td class="p-3 border">
                                <a href="#" class="text-blue-600 hover:underline mr-2">Review</a>
                                <a href="#" class="text-red-600 hover:underline">Reject</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white p-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 RP-FYP Management System</p>
        </div>
    </footer>

</body>

</html>
<script>
// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === "Escape") {
        document.querySelectorAll('.z-50').forEach(modal => modal.classList.add('hidden'));
    }
});
</script>