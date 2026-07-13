@extends('layouts.app')
@section('title', 'Audit Log')
@section('page-title', auth()->user()->isAdmin() && request()->routeIs('admin.audit.index') ? 'Semua Audit Log' : 'Audit Log Saya')
@section('breadcrumb', 'Riwayat aktivitas sistem')

@section('content')

<div class="ds-card">
    <div class="ds-card-header">
        <h5><i class="bi bi-journal-text" style="color: #2563eb;"></i> Riwayat Aktivitas</h5>
        <span style="font-size: .78rem; color: #94a3b8;">{{ $logs->total() }} log tercatat</span>
    </div>

    @if($logs->count() === 0)
        <div style="text-align: center; padding: 3rem 2rem; color: #94a3b8;">
            <i class="bi bi-inbox" style="font-size: 2.5rem; display: block; margin-bottom: .75rem;"></i>
            Belum ada aktivitas tercatat
        </div>
    @else
        <table class="ds-table">
            <thead>
                <tr>
                    @if(auth()->user()->isAdmin() && request()->routeIs('admin.audit.index'))
                        <th>User</th>
                    @endif
                    <th>Aksi</th>
                    <th>Keterangan</th>
                    <th>IP Address</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    @if(auth()->user()->isAdmin() && request()->routeIs('admin.audit.index'))
                    <td>
                        <div style="display: flex; align-items: center; gap: .5rem;">
                            <div style="width: 26px; height: 26px; background: #dbeafe; border-radius: 50%;
                                 display: flex; align-items: center; justify-content: center;
                                 font-size: .62rem; font-weight: 700; color: #1d4ed8;">
                                {{ strtoupper(substr($log->user->name ?? '?', 0, 2)) }}
                            </div>
                            <span style="font-size: .82rem;">{{ $log->user->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    @endif
                    <td>
                        <code style="font-size: .72rem; background: #eff6ff; color: #1d4ed8;
                              padding: .2rem .5rem; border-radius: 5px; font-weight: 600;">
                            {{ $log->action }}
                        </code>
                    </td>
                    <td style="font-size: .82rem; color: #475569;">{{ $log->description }}</td>
                    <td style="font-size: .75rem; color: #94a3b8;">{{ $log->ip_address ?? '—' }}</td>
                    <td style="font-size: .78rem; color: #94a3b8; white-space: nowrap;">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: center;">
            {{ $logs->links() }}
        </div>
    @endif
</div>

@endsection
