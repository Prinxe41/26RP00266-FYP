<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</head>

<body class="bg-gray-100 min-h-screen">
    <nav class="bg-green-700 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">RP-FYP HOD Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span>Welcome, {{ $hod->first_name }} {{ $hod->last_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="bg-green-800 px-4 py-2 rounded hover:bg-green-900">Logout</button>
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



    <main class="container mx-auto py-8 px-4">

        <div class="bg-white rounded shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Department Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Name</p>
                    <p class="font-semibold">{{ $hod->first_name }} {{ $hod->last_name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Email</p>
                    <p class="font-semibold">{{ $hod->hod_email }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Phone</p>
                    <p class="font-semibold">{{ $hod->hod_phone }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Department Code & Name</p>
                    <p class="font-semibold">{{ $hod->department_code }}-{{ $department->department_name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Campus</p>
                    <p class="font-semibold">{{ $campus->campus_name }}</p>
                </div>

            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded shadow-md p-6">
                <h3 class="text-xl font-bold mb-3">Students</h3>
                <p class="text-gray-600 mb-3">Manage department students</p>
                <button onclick="document.getElementById('studentsModal').classList.remove('hidden')"
                    class="text-green-600 hover:underline">View All Students</button>

            </div>

            <div class="bg-white rounded shadow-md p-6">
                <h3 class="text-xl font-bold mb-3">Supervisors</h3>
                <p class="text-gray-600 mb-3">Manage department supervisors</p>
                <div class="flex flex-col space-y-2">
                    <button onclick="document.getElementById('addSupervisorModal').classList.remove('hidden')"
                        class="text-green-600 hover:underline text-left">Add Supervisor</button>
                    <button onclick="document.getElementById('supervisorsModal').classList.remove('hidden')"
                        class="text-green-600 hover:underline text-left">View All Supervisors</button>

                </div>

            </div>

            <div class="bg-white rounded shadow-md p-6">
                <h3 class="text-xl font-bold mb-3">Projects</h3>
                <p class="text-gray-600 mb-3">Monitor all department projects</p>
                <button onclick="document.getElementById('projectsModal').classList.remove('hidden')"
                    class="text-green-600 hover:underline">View All Projects</button>

            </div>
        </div>

        <div class="bg-white rounded shadow-md p-6 mt-6">
            <h3 class="text-xl font-bold mb-4">Recent Activities</h3>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left p-3 border">Date</th>
                        <th class="text-left p-3 border">Student</th>
                        <th class="text-left p-3 border">Activity</th>
                        <th class="text-left p-3 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-3 border">2025-04-15</td>
                        <td class="p-3 border">John Doe</td>
                        <td class="p-3 border">Submitted Proposal</td>
                        <td class="p-3 border"><span
                                class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Pending</span></td>
                    </tr>
                    <tr>
                        <td class="p-3 border">2025-04-14</td>
                        <td class="p-3 border">Jane Smith</td>
                        <td class="p-3 border">Progress Report</td>
                        <td class="p-3 border"><span
                                class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Approved</span></td>
                    </tr>
                    <tr>
                        <td class="p-3 border">2025-04-12</td>
                        <td class="p-3 border">Mike Johnson</td>
                        <td class="p-3 border">Final Defense</td>
                        <td class="p-3 border"><span
                                class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Scheduled</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

    <footer class="bg-gray-800 text-white p-4 mt-8">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 RP-FYP Management System</p>
        </div>
    </footer>
    <!-- Students Modal -->
    <div id="studentsModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
        <div class="bg-white max-w-4xl w-full p-6 rounded shadow-lg relative">
            <h2 class="text-xl font-bold mb-4">Students in Your Department</h2>

            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">Reg Number</th>
                        <th class="border p-2">Full Name</th>
                        <th class="border p-2">Email</th>
                        <th class="border p-2">Phone</th>
                        <th class="border p-2">Action</th>
                        <th class="border p-2">Assigned Supervisor(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td class="border p-2">{{ $student->student_reg_number }}</td>
                            <td class="border p-2">{{ $student->student_first_name }} {{ $student->student_last_name }}</td>
                            <td class="border p-2">{{ $student->student_email }}</td>
                            <td class="border p-2">{{ $student->student_phone_number }}</td>
                            <td class="border p-2">
                                <button onclick="openAssignModal('{{ $student->student_reg_number }}')"
                                    class="text-blue-600 hover:underline text-sm">Add Supervisor</button>
                            </td>
                            <td class="border p-2">
                                @if($assignedSupervisors->has($student->student_reg_number))
                                    @foreach($assignedSupervisors[$student->student_reg_number] as $sup)
                                        <div class="text-sm">
                                            {{ $sup->supervisor_first_name }} {{ $sup->supervisor_last_name }}
                                            <form method="POST" action="{{ route('hod.unassign-supervisor') }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="project_code" value="{{ $sup->project_code }}">
                                                <input type="hidden" name="supervisor_email" value="{{ $sup->supervisor_email }}">
                                                <button type="submit"
                                                    class="text-red-500 text-xs ml-2 hover:underline">Unassign</button>
                                            </form>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-gray-400 text-sm">No supervisor</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border p-2 text-center text-gray-500">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex justify-end mt-4">
                <button onclick="document.getElementById('studentsModal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
            </div>
        </div>
    </div>
    <!-- Add Supervisor Modal -->
    <div id="addSupervisorModal"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
        <div class="bg-white w-full max-w-lg p-6 rounded shadow-lg">
            <h2 class="text-xl font-bold mb-4">Add Supervisor</h2>

            <form method="POST" action="{{ route('hod.store-supervisor') }}">
                @csrf

                <input type="email" name="supervisor_email" placeholder="Email"
                    class="w-full mb-3 border px-4 py-2 rounded" required>
                <input name="supervisor_first_name" placeholder="First Name"
                    class="w-full mb-3 border px-4 py-2 rounded" required>
                <input name="supervisor_last_name" placeholder="Last Name" class="w-full mb-3 border px-4 py-2 rounded"
                    required>
                <input name="supervisor_phone_number" placeholder="Phone Number"
                    class="w-full mb-3 border px-4 py-2 rounded" required>
                <input type="password" name="supervisor_password" placeholder="Password"
                    class="w-full mb-3 border px-4 py-2 rounded" required>

                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('addSupervisorModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Supervisors List Modal -->
    <div id="supervisorsModal"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
        <div class="bg-white max-w-4xl w-full p-6 rounded shadow-lg relative">
            <h2 class="text-xl font-bold mb-4">Supervisors in Your Department</h2>

            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Email</th>
                        <th class="border p-2">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($supervisors as $sup)
                        <tr>
                            <td class="border p-2">{{ $sup->supervisor_first_name }} {{ $sup->supervisor_last_name }}</td>
                            <td class="border p-2">{{ $sup->supervisor_email }}</td>
                            <td class="border p-2">{{ $sup->supervisor_phone_number }}</td>
                            <td class="border p-2 flex space-x-2">
                                <!-- Edit -->
                                <button onclick="openEditModal({{ json_encode($sup) }})"
                                    class="text-blue-600 hover:underline text-sm">Edit</button>
                                <!-- Delete -->
                                <form method="POST" action="{{ route('hod.delete-supervisor', $sup->supervisor_email) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this supervisor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm ml-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="border p-2 text-center text-gray-500">No supervisors found.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

            <div class="flex justify-end mt-4">
                <button onclick="document.getElementById('supervisorsModal').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Supervisor Modal -->
    <div id="editSupervisorModal"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
        <div class="bg-white w-full max-w-lg p-6 rounded shadow-lg">
            <h2 class="text-xl font-bold mb-4">Edit Supervisor</h2>

            <form method="POST" id="editSupervisorForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="original_email" id="edit_original_email">

                <input type="email" name="supervisor_email" id="edit_email" class="w-full mb-3 border px-4 py-2 rounded"
                    required>
                <input name="supervisor_first_name" id="edit_first_name" class="w-full mb-3 border px-4 py-2 rounded"
                    required>
                <input name="supervisor_last_name" id="edit_last_name" class="w-full mb-3 border px-4 py-2 rounded"
                    required>
                <input name="supervisor_phone_number" id="edit_phone" class="w-full mb-3 border px-4 py-2 rounded"
                    required>

                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('editSupervisorModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Assign Supervisor Modal -->
    <div id="assignSupervisorModal"
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
        <div class="bg-white w-full max-w-lg p-6 rounded shadow-lg">
            <h2 class="text-xl font-bold mb-4">Assign Supervisor to Student Project</h2>

            <form method="POST" action="{{ route('hod.assign-supervisor') }}">
                @csrf
                <div class="mb-3">
                    <label class="text-sm text-gray-700 block mb-1">Reg Number</label>
                    <input type="text" name="student_reg_number" id="assign_student_reg_number">
                </div>

                <div class="mb-3">
                    <label class="text-sm text-gray-700 block mb-1">Select Supervisor</label>
                    <select name="supervisor_email" required class="w-full border px-4 py-2 rounded">
                        <option value="">-- Choose Supervisor --</option>
                        @foreach ($supervisors as $sup)
                            <option value="{{ $sup->supervisor_email }}">
                                {{ $sup->supervisor_first_name }} {{ $sup->supervisor_last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button"
                        onclick="document.getElementById('assignSupervisorModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Assign</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Projects Modal -->
    <div id="projectsModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white max-w-6xl w-full p-6 rounded shadow-lg overflow-y-auto max-h-[80vh]">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">All Department Projects</h2>
                <button onclick="document.getElementById('projectsModal').classList.add('hidden')"
                    class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
            </div>

            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">Reg No</th>
                        <th class="border p-2">Project Name</th>
                        <th class="border p-2">Abstract</th>
                        <th class="border p-2">Supervisor</th>
                        <th class="border p-2">Status</th>
                        <th class="border p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                                    <tr>
                                        <td class="border p-2">{{ $project->student_reg_number }}</td>
                                        <td class="border p-2">{{ $project->project_name }}</td>
                                        <td class="border p-2 text-sm max-w-xs">
                                            <strong>Name:</strong> {{ $project->project_name }}<br>

                                            @if ($project->project_dissertation)
                                                <strong>Dissertation:</strong>
                                                <a href="{{ asset('storage/' . $project->project_dissertation) }}" target="_blank"
                                                    class="text-blue-600 hover:underline">View</a><br>
                                            @endif

                                            @if ($project->project_source_codes)
                                                <strong>Source Code:</strong>
                                                <a href="{{ asset('storage/' . $project->project_source_codes) }}" target="_blank"
                                                    class="text-blue-600 hover:underline">Download</a>
                                            @endif
                                        </td>

                                        <td class="border p-2">
                                            @if ($project->supervisor_names)
                                                                    @php
                                                                        $names = explode(',', $project->supervisor_names);
                                                                        $emails = explode(',', $project->supervisor_emails);
                                                                    @endphp
                                                                    @foreach ($names as $index => $name)
                                                                        <div class="text-sm">
                                                                            {{ $name }}<br>
                                                                            <span class="text-xs text-gray-500">{{ $emails[$index] ?? '' }}</span>
                                                                        </div>
                                                                    @endforeach
                                            @else
                                                <span class="text-gray-400 italic text-sm">Unassigned</span>
                                            @endif
                                        </td>

                                        <td class="border p-2 font-semibold">
                                            @if (is_null($project->approved))
                                                <span class="text-yellow-600">Pending</span>
                                            @elseif($project->approved)
                                                <span class="text-green-600">Approved</span>
                                            @else
                                                <span class="text-red-600">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="border p-2">
                                            @if (is_null($project->approved))
                                                <form method="POST" action="{{ route('hod.update-project-status') }}" class="space-y-2">
                                                    @csrf
                                                    <input type="hidden" name="project_code" value="{{ $project->project_code }}">

                                                    <textarea name="comment" rows="2"
                                                        placeholder="Optional comment (e.g. suggestions, reasons)"
                                                        class="w-full border rounded px-2 py-1 text-sm text-gray-700"></textarea>

                                                    <div class="flex space-x-2">
                                                        <button name="status" value="1"
                                                            class="text-green-600 hover:underline">Accept</button>
                                                        <button name="status" value="0" class="text-red-600 hover:underline">Reject</button>
                                                    </div>
                                                </form>

                                            @else
                                                <span class="text-gray-400 text-xs">Reviewed</span>
                                            @endif
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-3 text-gray-500">No projects submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
<script>
    function openEditModal(supervisor) {
        document.getElementById('edit_email').value = supervisor.supervisor_email;
        document.getElementById('edit_original_email').value = supervisor.supervisor_email;
        document.getElementById('edit_first_name').value = supervisor.supervisor_first_name;
        document.getElementById('edit_last_name').value = supervisor.supervisor_last_name;
        document.getElementById('edit_phone').value = supervisor.supervisor_phone_number;

        document.getElementById('editSupervisorForm').action =
            '/hod/update-supervisor/' + encodeURIComponent(supervisor.supervisor_email);

        document.getElementById('editSupervisorModal').classList.remove('hidden');
    }
</script>
<script>
    function openAssignModal(studentRegNumber) {
        document.getElementById('assign_student_reg_number').value = studentRegNumber;
        document.getElementById('assignSupervisorModal').classList.remove('hidden');
    }
</script>
<script>
    let inactivityTime = function () {
        let timer;
        const logoutMinutes = 3;
        const logoutMilliseconds = logoutMinutes * 60 * 1000;

        function resetTimer() {
            clearTimeout(timer);
            timer = setTimeout(logout, logoutMilliseconds);
        }

        function logout() {
            // Redirect to logout route
            window.location.href = "{{ route('logout') }}";
        }

        // Reset timer on any user interaction
        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;
        document.onscroll = resetTimer;
        document.onclick = resetTimer;
    };

    inactivityTime();
</script>