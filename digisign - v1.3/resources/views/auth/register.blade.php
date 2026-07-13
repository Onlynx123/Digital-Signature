@extends('layouts.auth')
@section('title', 'Daftar Akun')

@section('content')
<div class="auth-card">
    <div class="auth-logo">
        <div class="logo-icon"><i class="bi bi-pen-fill"></i></div>
        <h1>Buat Akun</h1>
        <p>Bergabung dengan DigiSign</p>
    </div>

    @if($errors->any())
    <div class="ds-alert error" style="margin-bottom: 1rem;">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <ul style="margin: 0; padding-left: 1.1rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div style="margin-bottom: 1rem;">
            <label class="ds-label" for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" class="ds-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   value="{{ old('name') }}" placeholder="John Doe" required autofocus>
        </div>

        <div style="margin-bottom: 1rem;">
            <label class="ds-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="ds-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}" placeholder="email@example.com" required>
        </div>

        <div style="margin-bottom: 1rem;">
            <label class="ds-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="ds-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="Minimal 8 karakter" required>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label class="ds-label" for="password_confirmation">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="ds-input" placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="btn-ds-primary" style="width: 100%; justify-content: center; padding: .7rem; font-size: .95rem;">
            <i class="bi bi-person-plus"></i>
            Daftar
        </button>
    </form>

    <div style="text-align: center; margin-top: 1.25rem; font-size: .82rem; color: var(--ds-gray-400);">
        Sudah punya akun?
        <a href="{{ route('login') }}" style="color: var(--ds-violet-600); font-weight: 600; text-decoration: none;">Masuk di sini</a>
    </div>
</div>
@endsection
