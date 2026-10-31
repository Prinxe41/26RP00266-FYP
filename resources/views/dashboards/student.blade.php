<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">RP-FYP Student Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span>Welcome, {{ $student->student_first_name }} {{ $student->student_last_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-blue-700 px-4 py-2 rounded hover:bg-blue-800">Logout</button>
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


    <main class="container mx-auto py-8 px-4 flex-1">
        <div class="bg-white rounded shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Your Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Registration Number</p>
                    <p class="font-semibold">{{ $student->student_reg_number }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Full Name</p>
                    <p class="font-semibold">{{ $student->student_first_name }} {{ $student->student_last_name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Email</p>
                    <p class="font-semibold">{{ $student->student_email }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Phone Number</p>
                    <p class="font-semibold">{{ $student->student_phone_number }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Gender</p>
                    <p class="font-semibold">{{ $student->student_gender }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Faculty (Option)</p>
                    <p class="font-semibold">{{ $faculty->faculty_name ?? 'N/A' }}</p>
                </div>

            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Final Year Project -->
            <!-- Final Year Project -->
            <div class="bg-white rounded shadow-md p-6">
                <h3 class="text-xl font-bold mb-3">Final Year Project</h3>

                @if ($project)
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li><strong>Project Name:</strong> {{ $project->project_name }}</li>
                        <li><strong>Submission Date:</strong> {{ $project->submission_date }}</li>
                        <li>
                            <strong>Status:</strong>
                            @if ($project->approved === null)
                                <span class="text-yellow-600 font-semibold">Pending</span>
                            @elseif ($project->approved)
                                <span class="text-green-600 font-semibold">Approved</span>
                            @else
                                <span class="text-red-600 font-semibold">Rejected</span><br>
                                <!-- Submit Button -->
                                <button onclick="document.getElementById('submitModal').classList.remove('hidden')"
                                    class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    Submit Another Project
                                </button>

                            @endif
                        </li>
                    </ul>

                    <!-- Conditionally show Edit/Delete if not yet approved -->
                    @if ($project->approved === null)
                        <div class="mt-4 flex space-x-2">
                            <!-- Edit Button -->
                            <button onclick="document.getElementById('editModal').classList.remove('hidden')"
                                class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                                Edit Project
                            </button>

                            <!-- Delete Button -->
                            <form action="{{ route('student.project.delete') }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this project?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="project_code" value="{{ $project->project_code }}">
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                    Delete Project
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <!-- Submit Button -->
                    <button onclick="document.getElementById('submitModal').classList.remove('hidden')"
                        class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Submit Project
                    </button>

                @endif


            </div>


            <div class="bg-white rounded shadow-md p-6">
                <h3 class="text-xl font-bold mb-3">Supervisor</h3>

                @if ($supervisor->count())
                    @foreach ($supervisor as $sup)
                        <div class="mb-3">
                            <p class="text-gray-600"><strong>Name:</strong> {{ $sup->supervisor_first_name }}
                                {{ $sup->supervisor_last_name }}
                            </p>
                            <p class="text-gray-600"><strong>Email:</strong> {{ $sup->supervisor_email }}</p>
                            <p class="text-gray-600"><strong>Phone:</strong> {{ $sup->supervisor_phone_number }}</p>
                            <div class="flex space-x-2 mt-2">
                                <a href="mailto:{{ $sup->supervisor_email }}" target="_blank"
                                    class="text-blue-600 hover:underline">
                                    Email
                                </a>
                                @if ($project)
                                    <a href="{{ route('chat.index', ['project_code' => $project->project_code, 'receiver_email' => $sup->supervisor_email]) }}"
                                        class="bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 flex items-center space-x-1 inline-flex">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span>Chat</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @if (!$loop->last)
                            <hr class="my-2">
                        @endif
                    @endforeach
                @else
                    <p class="text-gray-500 italic">You have not been assigned a supervisor yet.</p>
                @endif
            </div>



            <div class="bg-white rounded shadow-md p-6">
                <h3 class="text-xl font-bold mb-4">Your Project Submission</h3>

                @if ($project)
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li><strong>Project Name:</strong> {{ $project->project_name }}</li>
                        <li><strong>Problems:</strong> {{ $project->project_problems }}</li>
                        <li><strong>Solutions:</strong> {{ $project->project_solutions }}</li>
                        <li><strong>Status:</strong>
                            @if (is_null($project->approved))
                                <span class="text-yellow-600 font-semibold">Pending</span>
                            @elseif ($project->approved)
                                <span class="text-green-600 font-semibold">Approved</span>
                            @else
                                <span class="text-red-600 font-semibold">Rejected</span>
                            @endif
                        </li>

                        <li><strong>Submission Date:</strong> {{ $project->submission_date }}</li>
                        <li>
                            <a href="{{ asset('storage/' . $project->project_dissertation) }}" target="_blank"
                                class="text-blue-500 underline">
                                View Dissertation
                            </a>
                        </li>
                        <li>
                            <a href="{{ asset('storage/' . $project->project_source_codes) }}" target="_blank"
                                class="text-blue-500 underline">
                                Download Source Code
                            </a>
                        </li>
                    </ul>
                @else
                    <p class="text-gray-600">No project submitted yet.</p>
                @endif
            </div>


        </div>
    </main>

    <footer class="bg-gray-800 text-white p-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 RP-FYP Management System</p>
        </div>
    </footer>
    <!-- Project Submission Modal -->
    <div id="submitModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-xl">

            <h2 class="text-xl font-bold mb-4">Submit Final Year Project</h2>

            <form method="POST" action="{{ route('student.submit-project') }}" enctype="multipart/form-data">
                @csrf

                <input name="project_name" placeholder="Project Name" required
                    class="w-full mb-3 px-4 py-2 border rounded">

                <textarea name="project_problems" placeholder="Project Problems" rows="2"
                    class="w-full mb-3 px-4 py-2 border rounded" required></textarea>

                <textarea name="project_solutions" placeholder="Project Solutions" rows="2"
                    class="w-full mb-3 px-4 py-2 border rounded" required></textarea>

                <textarea name="project_abstract" placeholder="Project Abstract" rows="3"
                    class="w-full mb-3 px-4 py-2 border rounded" required></textarea>

                <div class="mb-3">
                    <label class="block text-sm mb-1 text-gray-600">Upload Dissertation</label>
                    <input type="file" name="project_dissertation" accept=".pdf,.docx"
                        class="w-full border px-4 py-2 rounded">
                </div>

                <div class="mb-3">
                    <label class="block text-sm mb-1 text-gray-600">Upload Source Code</label>
                    <input type="file" name="project_source_codes" accept=".zip,.rar"
                        class="w-full border px-4 py-2 rounded">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('submitModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Modal -->
    @if ($project)
        <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
            <div class="bg-white p-6 rounded-lg w-full max-w-2xl">
                <h2 class="text-xl font-semibold mb-4">Edit Your Project</h2>

                <form action="{{ route('student.project.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="project_code" value="{{ $project->project_code }}">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Project Name</label>
                        <input type="text" name="project_name" value="{{ $project->project_name }}" required
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Problems</label>
                        <textarea name="project_problems" rows="3" required
                            class="w-full border border-gray-300 rounded px-3 py-2">{{ $project->project_problems }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Solutions</label>
                        <textarea name="project_solutions" rows="3" required
                            class="w-full border border-gray-300 rounded px-3 py-2">{{ $project->project_solutions }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-small text-gray-700">Abstract</label>
                        <textarea name="project_abstract" rows="4" required
                            class="w-full border border-gray-300 rounded px-3 py-2">{{ $project->project_abstract }}</textarea>
                    </div>


                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Dissertation (PDF)</label>
                        <input type="file" name="project_dissertation" accept="application/pdf"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Source Codes (ZIP)</label>
                        <input type="file" name="project_source_codes" accept=".zip"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                            class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</body>

</html>