<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dhyecast - Diecast Store Hub & API</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-rose-500 selection:text-white">
    <!-- Navbar -->
    <header class="border-b border-slate-800 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 flex items-center justify-center shadow-lg shadow-rose-500/20">
                    <i class="fa-solid fa-car text-white text-lg"></i>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-wider text-white">DHYE<span class="text-rose-500">CAST</span></span>
                    <span class="text-xs px-2 py-0.5 ml-2 rounded bg-rose-500/10 text-rose-400 border border-rose-500/20 font-mono">v1.0 Hybrid</span>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm">
                <a href="http://127.0.0.1:5173" target="_blank" class="px-4 py-2 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 hover:bg-cyan-500/20 font-medium transition flex items-center">
                    <i class="fa-brands fa-react mr-2"></i> Buka Storefront (React)
                </a>
                <a href="http://127.0.0.1:5173/admin" target="_blank" class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold transition shadow-lg shadow-amber-500/20 flex items-center">
                    <i class="fas fa-gauge-high mr-2"></i> Admin Panel (React Utama)
                </a>
            </div>
        </div>
    </header>

    <!-- Main Hero & Cards -->
    <main class="max-w-7xl mx-auto px-6 py-12 flex-grow flex flex-col justify-center">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-xs text-slate-300 mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>RESTful API + React 19 + Laravel 11 Blade Hybrid Architecture</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight mb-4">
                Pusat Integrasi Portal <span class="bg-gradient-to-r from-rose-500 via-amber-400 to-rose-400 bg-clip-text text-transparent">Dhyecast</span>
            </h1>
            <p class="text-slate-400 text-base md:text-lg">
                Implementasi modul praktikum <strong>BAB III REST API</strong> & <strong>Modul Backpage Laravel 9</strong> dengan database terpusat di Laragon MySQL.
            </p>
        </div>

        <!-- 3 Architecture Portals -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: React Storefront -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-cyan-500/50 transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                        <i class="fa-brands fa-react"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">1. React SPA Storefront</h3>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">
                        Katalog diecast dinamis, filter kategori (Hot Wheels, Mini GT, Tomica, LBWK), search realtime, cart drawer, dan guest checkout terintegrasi langsung via Laravel RESTful API.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-800/80">
                    <a href="http://127.0.0.1:5173" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-sm transition">
                        Buka React App (Port 5173) <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2: React Admin Dashboard -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-amber-500/50 transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">2. React Admin Dashboard</h3>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">
                        Panel kontrol admin modern dengan autentikasi Laravel Sanctum, manajemen status pesanan real-time, live order modal, dan statistik pendapatan.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-800/80">
                    <a href="http://127.0.0.1:5173/admin" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm transition">
                        Login React Admin <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Card 3: Laravel Blade Backpage -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-rose-500/50 transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                        <i class="fa-solid fa-code"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">3. Laravel Blade Backpage</h3>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">
                        Sesuai panduan <strong>Modul Backpage Laravel 9</strong>: AdminLayout Tailwind, CRUD produk diecast lengkap, form validasi CSRF, upload gambar produk, pagination, pencarian <code>?s=</code>, dan filter kategori.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-800/80">
                    <a href="{{ route('admin.products.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-sm transition">
                        Buka Blade Admin (Kembaran React) <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- API Reference Banner -->
        <div class="mt-8 p-6 rounded-2xl bg-slate-900/60 border border-slate-800 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <i class="fas fa-network-wired"></i>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm">REST API Endpoints Siap Pakai (Sanctum Ready)</h4>
                    <p class="text-xs text-slate-400"><code>GET /api/products</code> &bull; <code>POST /api/orders</code> &bull; <code>POST /api/login</code> &bull; <code>GET /api/admin/stats</code></p>
                </div>
            </div>
            <a href="{{ url('/api/products') }}" target="_blank" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-mono transition">
                Test API Response JSON &rarr;
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/40 py-6 text-center text-xs text-slate-500">
        Dhyecast &bull; Praktikum Pemrograman Web (P5-121026) &bull; Dhy & Pair AI Assistant
    </footer>
</body>
</html>
