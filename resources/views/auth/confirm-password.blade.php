<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Konfirmasi Password</h2>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-2">
            Ini adalah area aman aplikasi. Silakan konfirmasi password Anda sebelum melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <x-ui.input
            name="password"
            type="password"
            label="Password"
            placeholder="••••••••"
            :error="$errors->first('password')"
            class="mb-6"
            required
        />

        <x-ui.button type="submit" class="w-full">
            Konfirmasi
        </x-ui.button>
    </form>
</x-guest-layout>
