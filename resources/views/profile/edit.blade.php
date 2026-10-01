@extends('layouts.app')
@section('title', 'Profil')
@section('content')
    <div class="mb-6"><h1>Profil</h1><p class="mt-1 text-sm text-[hsl(var(--muted-foreground))]">Kelola akun dan keamanan Anda</p></div>

    <div class="space-y-6">
        <div class="max-w-4xl space-y-6">
            <div class="card p-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card p-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card p-6">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
