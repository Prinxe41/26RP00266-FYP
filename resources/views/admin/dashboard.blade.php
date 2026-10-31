<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Additional CSS -->
    @yield('styles')
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <!-- Top Navigation -->
        <nav class="bg-gray-800 text-white shadow-md">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <a class="text-xl font-bold" href="{{ route('admin.manage-campus') }}">Admin Dashboard</a>

                <!-- User dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-1 hover:text-gray-300">
                        <span>{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex flex-1">
            <!-- Sidebar -->
            <div class="bg-gray-800 text-white w-64 flex-shrink-0 shadow-md">
                <div class="py-4">
                    <ul class="space-y-2 px-2">
                        <li>
                            <a href="{{ route('admin.manage-campus') }}"
                                class="block px-4 py-2 rounded {{ request()->routeIs('admin.manage-campus') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                                Manage Campus
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.manage-department') }}"
                                class="block px-4 py-2 rounded {{ request()->routeIs('admin.manage-department') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                                Manage Department
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.manage-faculty') }}"
                                class="block px-4 py-2 rounded {{ request()->routeIs('admin.manage-faculty') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                                Manage Faculty
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.manage-hod') }}"
                                class="block px-4 py-2 rounded {{ request()->routeIs('admin.manage-hod') ? 'bg-blue-600' : 'hover:bg-gray-700' }}">
                                Manage HOD
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 p-6">
                <!-- @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif -->

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Additional JS -->
    @yield('scripts')
</body>

</html>