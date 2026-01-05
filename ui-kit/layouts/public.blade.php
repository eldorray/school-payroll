<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Portfolio') }}</title>
        <meta name="description" content="{{ $description ?? 'Professional portfolio showcasing my work and skills' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-[hsl(var(--background))]">
            <!-- Navigation - Apple Style -->
            <nav class="nav-apple" 
                 x-data="{ scrolled: false, mobileOpen: false }" 
                 @scroll.window="scrolled = window.scrollY > 50"
                 :class="{ 'scrolled': scrolled }">
                <div class="container-tight">
                    <div class="flex items-center justify-between h-16">
                        <!-- Logo -->
                        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-[hsl(var(--foreground))]">
                            <div class="w-8 h-8 rounded-lg bg-[hsl(var(--primary))] flex items-center justify-center">
                                <span class="text-sm font-bold text-[hsl(var(--primary-foreground))]">P</span>
                            </div>
                            <span class="hidden sm:inline">Portfolio</span>
                        </a>

                        <!-- Desktop Navigation -->
                        <div class="hidden md:flex items-center gap-8">
                            <a href="{{ route('home') }}#about" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] transition-colors">
                                About
                            </a>
                            <a href="{{ route('home') }}#skills" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] transition-colors">
                                Skills
                            </a>
                            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] transition-colors">
                                Projects
                            </a>
                            <a href="{{ route('home') }}#experience" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] transition-colors">
                                Experience
                            </a>
                            <a href="{{ route('home') }}#contact" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] transition-colors">
                                Contact
                            </a>
                        </div>

                        <!-- Right Side -->
                        <div class="flex items-center gap-4">
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">
                                    Login
                                </a>
                            @endauth

                            <!-- Mobile Menu Button -->
                            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg hover:bg-[hsl(var(--accent))]">
                                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Menu -->
                    <div x-show="mobileOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         x-cloak
                         class="md:hidden py-4 border-t border-[hsl(var(--border))]">
                        <div class="flex flex-col gap-4">
                            <a href="{{ route('home') }}#about" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">About</a>
                            <a href="{{ route('home') }}#skills" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">Skills</a>
                            <a href="{{ route('projects.index') }}" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">Projects</a>
                            <a href="{{ route('home') }}#experience" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">Experience</a>
                            <a href="{{ route('home') }}#contact" class="text-sm font-medium text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))]">Contact</a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="border-t border-[hsl(var(--border))] py-12 mt-20">
                <div class="container-tight">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">
                            © {{ date('Y') }} Portfolio. Built with Laravel & ❤️
                        </p>
                        <div class="flex items-center gap-6">
                            @if(isset($profile) && $profile)
                                @if($profile->github_url)
                                    <a href="{{ $profile->github_url }}" target="_blank" class="text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                        </svg>
                                    </a>
                                @endif
                                @if($profile->linkedin_url)
                                    <a href="{{ $profile->linkedin_url }}" target="_blank" class="text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                        </svg>
                                    </a>
                                @endif
                                @if($profile->twitter_url)
                                    <a href="{{ $profile->twitter_url }}" target="_blank" class="text-[hsl(var(--muted-foreground))] hover:text-[hsl(var(--foreground))] transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </body>
</html>
