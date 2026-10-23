<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts & Tailwind CDN -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            body { font-family: 'Inter', system-ui, sans-serif; }
        </style>
    </head>
    <body class="font-sans text-gray-900 bg-gray-100 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 px-4">
            <div class="text-center">
                <a href="{{ route('admin.dashboard') }}" class="text-3xl font-black tracking-tighter text-gray-900">
                    DHYECAST<span class="text-gray-400">.</span>
                </a>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mt-1">Admin Portal Backoffice</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white border border-gray-200 shadow-sm rounded-sm">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
