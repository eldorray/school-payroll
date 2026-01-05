<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'School Payroll') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen gradient-mesh flex flex-col justify-center items-center p-6">
            <!-- Logo -->
            <div class="mb-6 animate-fade-up">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="w-20 h-20 object-contain">
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
