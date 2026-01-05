<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Reset Password</h2>
            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Masukkan password baru Anda</p>
        </div>

        <!-- Email Address -->
        <x-ui.input
            name="email"
            type="email"
            label="Alamat Email"
            :value="old('email', $request->email)"
            :error="$errors->first('email')"
            class="mb-4"
            required
            autofocus
        />

        <!-- Password -->
        <x-ui.input
            name="password"
            type="password"
            label="Password Baru"
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
            Reset Password
        </x-ui.button>
    </form>
</x-guest-layout>
