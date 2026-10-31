<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h1 class="text-2xl font-bold mb-4 text-center">RP-FYP Login</h1>
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
        <form method="POST" action="/login">
            @csrf
            <input name="email_or_reg" placeholder="Email or Registration Number(Student)"
                class="w-full px-4 py-2 border rounded mb-3" required value="{{ old('email_or_reg') }}">
            <input type="password" name="password" placeholder="Password" class="w-full px-4 py-2 border rounded mb-3"
                required>
            <button class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                Login
            </button>
        </form>
        <div class="text-center mt-4">
            <a href="{{ route('register') }}" class="text-blue-500">Register as Student</a>
        </div>
    </div>
</body>

</html>