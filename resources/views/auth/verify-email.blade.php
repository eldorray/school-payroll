<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Verifikasi Email</h2>
        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-2">
            Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik link yang kami kirimkan. Jika tidak menerima email, kami akan dengan senang hati mengirimkan yang baru.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-ui.alert type="success" class="mb-4">
            Link verifikasi baru telah dikirim ke alamat email Anda.
        </x-ui.alert>
    @endif

    <div class="flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit">
                Kirim Ulang Email
            </x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="ghost">
                Logout
            </x-ui.button>
        </form>
    </div>
</x-guest-layout>
