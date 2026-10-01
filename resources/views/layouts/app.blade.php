<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Ringkasan') · School Payroll</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" x-data="{ sidebarOpen: false }" :class="{ 'overflow-hidden lg:overflow-auto': sidebarOpen }" @keydown.escape.window="if (sidebarOpen) { sidebarOpen = false; $refs.menuToggle.focus() }">
    @php $currentUnit = \App\Models\Unit::find(session('unit_id')); @endphp
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:z-[60] focus:bg-white focus:p-3">Lewati navigasi</a>
    <div x-cloak x-show="sidebarOpen"
         x-transition:enter="sidebar-backdrop-transition" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="sidebar-backdrop-transition" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false; $refs.menuToggle.focus()" class="fixed inset-0 bg-slate-950/50 z-40 lg:hidden"></div>
    <aside x-cloak id="app-sidebar" class="sidebar" :class="{ 'is-open': sidebarOpen }">
        <div class="p-4">
            <div class="sidebar-brand">
                <img src="{{ asset('logo.png') }}" alt="" class="w-10 h-10 object-contain rounded-lg bg-white p-1">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-white truncate">{{ $currentUnit?->name ?? 'School Payroll' }}</p>
                    <p class="text-xs text-slate-400">Administrasi penggajian</p>
                </div>
                <button type="button" @click="sidebarOpen = false; $refs.menuToggle.focus()" class="lg:hidden p-1" aria-label="Tutup navigasi">✕</button>
            </div>
        </div>
<nav aria-label="Navigasi utama" class="flex-1 px-4 pb-6 overflow-y-auto">
<a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Ringkasan
                    </a>
<p class="sidebar-label">Penggajian</p>
<a href="{{ route('payrolls.index') }}" @if(request()->routeIs('payrolls.*')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('payrolls.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Honor Guru
                    </a>
<a href="{{ route('extracurricular-payrolls.index') }}" @if(request()->routeIs('extracurricular-payrolls.*')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('extracurricular-payrolls.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Gaji Ekskul
                    </a>
@if($currentUnit && str_contains(strtolower($currentUnit->name), 'daarul hikmah'))
<a href="{{ route('tahfidz-payrolls.index') }}" @if(request()->routeIs('tahfidz-payrolls.*')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('tahfidz-payrolls.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Gaji Tahfidz
                    </a>
@endif
<p class="sidebar-label">Data</p>
<a href="{{ route('teachers.index') }}" @if(request()->routeIs('teachers.*')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Guru
                    </a>
<a href="{{ route('extracurriculars.index') }}" @if(request()->routeIs('extracurriculars.*')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('extracurriculars.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Ekskul
                    </a>
<a href="{{ route('academic-years.index') }}" @if(request()->routeIs('academic-years.*')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Tahun Ajaran
                    </a>
<p class="sidebar-label">Sistem</p>
<a href="{{ route('units.edit') }}" @if(request()->routeIs('units.*')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan Unit
                    </a>
<a href="{{ route('backups.index') }}" @if(request()->routeIs('backups.*')) aria-current="page" @endif
                       class="sidebar-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                        Backup & Restore
                    </a>
</nav>
        <div class="sidebar-user mx-4 mb-3">
            <a href="{{ route('profile.edit') }}" @if(request()->routeIs('profile.*')) aria-current="page" @endif class="flex items-center gap-3 flex-1 min-w-0" aria-label="Pengaturan profil">
                <span class="w-9 h-9 rounded-full bg-slate-700 text-white flex items-center justify-center shrink-0">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <span class="truncate text-white text-sm">{{ auth()->user()->name }}</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-2 rounded-lg hover:bg-slate-800" aria-label="Keluar" title="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H5v14h4m5-14v14m-3-7h10m-3-3 3 3-3 3"/></svg>
                </button>
            </form>
        </div>
    </aside>
    <div class="lg:pl-64 min-h-screen">
        <header class="lg:hidden flex items-center justify-between px-4 h-16 bg-white border-b">
            <button type="button" x-ref="menuToggle" @click="sidebarOpen = !sidebarOpen" :aria-expanded="sidebarOpen" aria-controls="app-sidebar" aria-label="Buka navigasi" class="p-2 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="font-semibold truncate">{{ $currentUnit?->name ?? 'School Payroll' }}</span>
            <a href="{{ route('profile.edit') }}" @if(request()->routeIs('profile.*')) aria-current="page" @endif class="p-2" aria-label="Profil">{{ mb_substr(auth()->user()->name, 0, 1) }}</a>
        </header>
        <main id="main-content" class="app-main">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6">
                        <x-ui.alert type="success" dismissible>
                            {{ session('success') }}
                        </x-ui.alert>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6">
                        <x-ui.alert type="error" dismissible>
                            {{ session('error') }}
                        </x-ui.alert>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6">
                        <x-ui.alert type="error" dismissible>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    </div>
                @endif

            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>
    <x-delete-confirmation />
</body>
</html>
