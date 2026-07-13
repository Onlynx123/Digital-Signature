@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Selamat datang, ' . auth()->user()->name)

@section('topbar-actions')
    <a href="{{ route('documents.create') }}" class="btn-ds-primary">
        <i class="bi bi-plus-lg"></i> Upload Dokumen
    </a>
@endsection

@section('content')

{{-- ===== STAT CARDS ===== --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">

    <div class="stat-card">
        <div class="stat-icon violet"><i class="bi bi-file-earmark-pdf"></i></div>
        <div class="stat-label">Total Dokumen</div>
        <div class="stat-number">{{ $stats['total'] }}</div>
        <div class="stat-sub">Semua dokumen Anda</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-label">Menunggu Tanda Tangan</div>
        <div class="stat-number" style="color: var(--ds-warn-fg);">{{ $stats['pending'] }}</div>
        <div class="stat-sub">Perlu tindakan</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
        <div class="stat-label">Selesai</div>
        <div class="stat-number" style="color: var(--ds-success-fg);">{{ $stats['completed'] }}</div>
        <div class="stat-sub">Semua pihak sudah tanda tangan</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon ink"><i class="bi bi-lock-fill"></i></div>
        <div class="stat-label">Terkunci</div>
        <div class="stat-number" style="color: var(--ds-violet-700);">{{ $stats['locked'] }}</div>
        <div class="stat-sub">Read-only, tidak dapat diubah</div>
    </div>

</div>

<div style="display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem;">

    {{-- ===== RECENT ACTIVITY ===== --}}
    <div class="ds-card">
        <div class="ds-card-header">
            <h5><i class="bi bi-clock-history" style="color: var(--ds-violet-600);"></i> Aktivitas Terbaru</h5>
            <a href="{{ route('audit.index') }}" style="font-size: .78rem; color: var(--ds-violet-600); text-decoration: none;">
                Lihat semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div style="padding: .5rem 1.5rem;">
            @forelse($recentActivity as $log)
            <div class="audit-row">
                <div class="audit-dot" style="margin-top: .5rem;"></div>
                <div>
                    <div class="audit-action">{{ $log->action }}</div>
                    <div class="audit-desc">{{ $log->description }}</div>
                    <div class="audit-time">
                        @if(auth()->user()->isAdmin() && isset($log->user))
                            {{ $log->user->name }} ·
                        @endif
                        {{ $log->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 2rem; color: var(--ds-gray-400); font-size: .875rem;">
                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: .5rem;"></i>
                Belum ada aktivitas
            </div>
            @endforelse
        </div>
    </div>

    {{-- ===== PENDING TO SIGN ===== --}}
    <div class="ds-card">
        <div class="ds-card-header">
            <h5><i class="bi bi-pen" style="color: var(--ds-warn-fg);"></i> Perlu Tanda Tangan</h5>
            @if($pendingToSign->count() > 0)
                <span class="ds-badge pending">{{ $pendingToSign->count() }} menunggu</span>
            @endif
        </div>

        @forelse($pendingToSign as $signer)
        <div class="pending-item">
            <div class="doc-icon">
                <i class="bi bi-file-earmark-pdf"></i>
            </div>
            <div class="doc-info">
                <div class="doc-title">{{ Str::limit($signer->document->title, 30) }}</div>
                <div class="doc-meta">Diminta oleh {{ $signer->document->owner->name }}</div>
            </div>
            <a href="{{ route('signatures.show', $signer->document) }}" class="btn-ds-primary" style="padding: .4rem .75rem; font-size: .78rem; white-space: nowrap;">
                <i class="bi bi-pen"></i> Sign
            </a>
        </div>
        @empty
        <div style="text-align: center; padding: 2.5rem 1rem; color: var(--ds-gray-400); font-size: .85rem;">
            <i class="bi bi-check-circle" style="font-size: 2rem; display: block; color: #9bdcb3; margin-bottom: .5rem;"></i>
            Tidak ada dokumen yang perlu ditandatangani
        </div>
        @endforelse

        @if($pendingToSign->count() > 0)
        <div style="padding: .75rem 1.5rem; border-top: 1px solid var(--ds-gray-100); text-align: center;">
            <a href="{{ route('signatures.pending') }}" style="font-size: .78rem; color: var(--ds-violet-600); text-decoration: none;">
                Lihat semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        @endif
    </div>

</div>

@endsection
