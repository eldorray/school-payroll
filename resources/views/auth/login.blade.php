<x-guest-layout>
    <!-- Session Status -->
    @if(session('status'))
        <x-ui.alert type="success" class="mb-4">
            {{ session('status') }}
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Welcome Text -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Selamat Datang</h2>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Masuk ke akun Anda</p>
        </div>

        <!-- Unit Selection -->
        <div class="mb-4">
            <x-ui.select
                name="unit_id"
                label="Pilih Unit"
                :options="\App\Models\Unit::pluck('name', 'id')->toArray()"
                placeholder="-- Pilih Unit Sekolah --"
                :error="$errors->first('unit_id')"
                required
            />
        </div>

        <!-- Email Address -->
        <x-ui.input
            name="email"
            type="email"
            label="Alamat Email"
            placeholder="email@contoh.com"
            :value="old('email')"
            :error="$errors->first('email')"
            class="mb-4"
            required
            autofocus
        />

        <!-- Password -->
        <x-ui.input
            name="password"
            type="password"
            label="Password"
            placeholder="••••••••"
            :error="$errors->first('password')"
            class="mb-4"
            required
        />

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-[hsl(var(--border))] text-[hsl(var(--primary))] focus:ring-[hsl(var(--ring))]" name="remember">
                <span class="ml-2 text-sm text-[hsl(var(--muted-foreground))]">Ingat saya</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="text-sm text-[hsl(var(--primary))] hover:underline" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <x-ui.button type="submit" class="w-full">
            Masuk
        </x-ui.button>
        
        <!-- Register Link -->
        @if (Route::has('register'))
            <p class="text-center mt-6 text-sm text-[hsl(var(--muted-foreground))]">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-[hsl(var(--primary))] hover:underline">Daftar</a>
            </p>
        @endif
    </form>
</x-guest-layout>
