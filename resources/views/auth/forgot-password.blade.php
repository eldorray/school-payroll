<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Lupa Password?</h2>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-2">
            Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan link untuk reset password.
        </p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <x-ui.alert type="success" class="mb-4">
            {{ session('status') }}
        </x-ui.alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <x-ui.input
            name="email"
            type="email"
            label="Alamat Email"
            placeholder="email@contoh.com"
            :value="old('email')"
            :error="$errors->first('email')"
            class="mb-6"
            required
            autofocus
        />

        <x-ui.button type="submit" class="w-full">
            Kirim Link Reset Password
        </x-ui.button>

        <p class="text-center mt-6 text-sm text-[hsl(var(--muted-foreground))]">
            <a href="{{ route('login') }}" class="text-[hsl(var(--primary))] hover:underline">Kembali ke halaman login</a>
        </p>
    </form>
</x-guest-layout>
