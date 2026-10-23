<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Products Management' }} - Dhyecast Admin</title>

    <!-- Tailwind CSS CDN & Inter font -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 overflow-hidden">
    <div class="flex h-screen bg-gray-50 overflow-hidden">
        <!-- Sidebar (Identik 1:1 dengan Sidebar.jsx) -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 flex flex-col justify-between h-screen hidden md:flex border-r border-gray-800">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                    <a href="{{ route('admin.dashboard') }}" class="text-2xl font-black tracking-tighter text-white">
                        DHYECAST<span class="text-gray-500">.</span>
                    </a>
                </div>

                <!-- Navigation List -->
                <nav class="mt-6 px-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-sm transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black font-semibold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-3 rounded-sm transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-white text-black font-semibold' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Products Management
                    </a>
                </nav>
            </div>

            <!-- Bottom Action -->
            <div class="p-4 border-t border-gray-800">
                <a href="http://127.0.0.1:5173" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-sm hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Store
                </a>
            </div>
        </aside>

        <!-- Main Content Area (Identik 1:1 dengan AdminLayout.jsx) -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Header -->
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard Overview' }}</h2>
                </div>

                <!-- Admin Profile indicator & Logout -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="h-8 w-8 rounded-full bg-black text-white flex items-center justify-center font-bold text-sm uppercase">
                            {{ substr(auth()->user()?->name ?? 'A', 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden sm:block">
                            {{ auth()->user()?->name ?? 'Admin' }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline m-0">
                        @csrf
                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 border border-gray-300 rounded-sm text-gray-700 hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition-colors cursor-pointer">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Dynamic Slot for Admin Page -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                @if(session('success'))
                    <div class="mb-4 bg-gray-900 text-white px-4 py-3 rounded-sm text-sm flex items-center justify-between shadow-sm">
                        <span>{{ session('success') }}</span>
                        <span class="text-xs text-gray-400">Tersimpan di Database</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-600 text-white px-4 py-3 rounded-sm text-sm flex items-center justify-between shadow-sm">
                        <span>{{ session('error') }}</span>
                        <span class="text-xs text-red-200">Gagal Memproses</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
