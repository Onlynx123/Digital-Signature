@extends('layouts.app')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('breadcrumb', 'Admin → Daftar semua user')

@section('content')

<div class="ds-card">
    <div class="ds-card-header">
        <h5><i class="bi bi-people" style="color: #2563eb;"></i> Semua User</h5>
        <span style="font-size: .78rem; color: #94a3b8;">{{ $users->total() }} user terdaftar</span>
    </div>

    <table class="ds-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Terdaftar</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: .6rem;">
                        <div style="width: 32px; height: 32px; background: #dbeafe; border-radius: 50%;
                             display: flex; align-items: center; justify-content: center;
                             font-size: .72rem; font-weight: 700; color: #1d4ed8;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <span style="font-weight: 500;">{{ $user->name }}</span>
                        @if($user->id === auth()->id())
                            <span style="font-size: .65rem; background: #f1f5f9; color: #64748b; padding: 1px 6px; border-radius: 8px;">Anda</span>
                        @endif
                    </div>
                </td>
                <td style="font-size: .82rem; color: #64748b;">{{ $user->email }}</td>
                <td><span class="ds-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                <td>
                    @if($user->is_active)
                        <span class="ds-badge signed">Aktif</span>
                    @else
                        <span class="ds-badge draft">Nonaktif</span>
                    @endif
                </td>
                <td style="font-size: .78rem; color: #94a3b8;">{{ $user->created_at->format('d M Y') }}</td>
                <td style="text-align: right;">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-ds-outline" style="padding: .35rem .75rem; font-size: .75rem;">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: center;">
        {{ $users->links() }}
    </div>
</div>

@endsection
