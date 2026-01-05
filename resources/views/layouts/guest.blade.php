<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'School Payroll') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen gradient-mesh flex flex-col justify-center items-center p-6">
            <!-- Logo -->
            <div class="glass w-20 h-20 rounded-2xl flex items-center justify-center mb-6 animate-fade-up">
                <svg class="w-10 h-10 text-[hsl(var(--primary))]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <!-- Title -->
            <h1 class="text-3xl font-bold text-[hsl(var(--foreground))] mb-2 animate-fade-up delay-100">School Payroll</h1>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mb-8 animate-fade-up delay-200">Management System</p>

            <!-- Card -->
            <div class="card glass w-full sm:max-w-md px-8 py-10 animate-scale-in delay-300">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            <p class="text-xs text-[hsl(var(--muted-foreground))] mt-8 animate-fade-up delay-400">
                &copy; {{ date('Y') }} School Payroll. Design by elfahmie.
            </p>
        </div>
    </body>
</html>
