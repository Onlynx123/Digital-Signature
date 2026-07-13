@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <div class="logo-icon">🔏</div>
        <h1>DigiSign</h1>
        <p>Digital Signature — Proof of Concept</p>
    </div>

    @if($errors->any())
    <div class="ds-alert error" style="margin-bottom: 1rem;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>{{ $errors->first() }}</div>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div style="margin-bottom: 1rem;">
            <label class="ds-label" for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="ds-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                value="{{ old('email') }}"
                placeholder="email@example.com"
                required
                autofocus
            >
        </div>

        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: .4rem;">
                <label class="ds-label" for="password" style="margin-bottom: 0;">Password</label>
            </div>
            <input
                type="password"
                id="password"
                name="password"
                class="ds-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                placeholder="••••••••"
                required
            >
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <label style="display: flex; align-items: center; gap: .4rem; font-size: .82rem; color: #475569; cursor: pointer;">
                <input type="checkbox" name="remember" style="accent-color: #2563eb;">
                Ingat saya
            </label>
        </div>

        <button type="submit" class="btn-ds-primary" style="width: 100%; justify-content: center; padding: .7rem; font-size: .95rem;">
            <i class="bi bi-box-arrow-in-right"></i>
            Masuk
        </button>
    </form>

    <div style="text-align: center; margin-top: 1.25rem; font-size: .82rem; color: #94a3b8;">
        Belum punya akun?
        <a href="{{ route('register') }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">Daftar sekarang</a>
    </div>

    {{-- Demo accounts hint --}}
    <div style="margin-top: 1.5rem; padding: .85rem; background: #eff6ff; border-radius: 10px; border: 1px solid #dbeafe;">
        <div style="font-size: .7rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .4rem;">
            Akun Demo
        </div>
        <div style="font-size: .75rem; color: #1e40af;">
            <div><strong>Admin:</strong> admin@digisign.test / password123</div>
            <div><strong>User:</strong> john@digisign.test / password123</div>
        </div>
    </div>
</div>
@endsection
