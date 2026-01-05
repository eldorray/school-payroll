<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Welcome Text -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Buat Akun</h2>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Daftar untuk mulai menggunakan sistem</p>
        </div>

        <!-- Name -->
        <x-ui.input
            name="name"
            label="Nama Lengkap"
            :value="old('name')"
            :error="$errors->first('name')"
            class="mb-4"
            required
            autofocus
        />

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

        <!-- Confirm Password -->
        <x-ui.input
            name="password_confirmation"
            type="password"
            label="Konfirmasi Password"
            placeholder="••••••••"
            :error="$errors->first('password_confirmation')"
            class="mb-6"
            required
        />

        <x-ui.button type="submit" class="w-full">
            Daftar
        </x-ui.button>

        <p class="text-center mt-6 text-sm text-[hsl(var(--muted-foreground))]">
            Sudah punya akun? 
            <a href="{{ route('login') }}" class="text-[hsl(var(--primary))] hover:underline">Masuk</a>
        </p>
    </form>
</x-guest-layout>
