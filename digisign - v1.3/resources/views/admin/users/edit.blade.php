@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('breadcrumb', 'Admin → Kelola User → Edit')

@section('content')

<div style="max-width: 520px;">
    <div class="ds-card">
        <div class="ds-card-header">
            <h5><i class="bi bi-person-gear" style="color: #2563eb;"></i> Edit: {{ $user->name }}</h5>
        </div>
        <div class="ds-card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 1rem;">
                    <label class="ds-label">Nama</label>
                    <input type="text" name="name" class="ds-input" value="{{ old('name', $user->name) }}" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="ds-label">Email</label>
                    <input type="email" name="email" class="ds-input" value="{{ old('email', $user->email) }}" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="ds-label">Role</label>
                    <select name="role" class="ds-select" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="user"  {{ $user->role === 'user'  ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <div class="ds-hint">Anda tidak dapat mengubah role Anda sendiri</div>
                    @endif
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: .5rem; font-size: .85rem; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1"
                               {{ $user->is_active ? 'checked' : '' }}
                               {{ $user->id === auth()->id() ? 'disabled' : '' }}
                               style="accent-color: #2563eb; width: 16px; height: 16px;">
                        Akun Aktif
                    </label>
                </div>

                <div style="display: flex; gap: .75rem; justify-content: flex-end;">
                    <a href="{{ route('admin.users.index') }}" class="btn-ds-outline">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn-ds-primary">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
