<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Welcome Text -->
        <div class="text-center mb-6">
            <h2 style="font-size: 22px; font-weight: 600; color: #1d1d1f; margin-bottom: 4px;">Welcome Back</h2>
            <p style="font-size: 14px; color: #86868b;">Sign in to your account</p>
        </div>

        <!-- Unit Selection -->
        <div class="mb-4">
            <label for="unit_id" class="login-label">Select Unit</label>
            <select id="unit_id" name="unit_id" class="login-input" required>
                <option value="">-- Choose School Unit --</option>
                @foreach(\App\Models\Unit::all() as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
            @error('unit_id')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="login-label">Email Address</label>
            <input id="email" class="login-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="login-label">Password</label>
            <input id="password" class="login-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="checkbox-modern" name="remember">
                <span style="margin-left: 8px; font-size: 13px; color: #86868b;">Remember me</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="login-link" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="login-btn">
            Sign In
        </button>
        
        <!-- Register Link -->
        @if (Route::has('register'))
            <p style="text-align: center; margin-top: 20px; font-size: 13px; color: #86868b;">
                Don't have an account? 
                <a href="{{ route('register') }}" class="login-link">Create one</a>
            </p>
        @endif
    </form>
</x-guest-layout>
