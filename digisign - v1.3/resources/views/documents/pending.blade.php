@extends('layouts.app')
@section('title', 'Pending Tanda Tangan')
@section('page-title', 'Dokumen Menunggu Tanda Tangan')
@section('breadcrumb', 'Dokumen yang perlu Anda tandatangani')

@section('content')

@if($pendingSigners->count() === 0)
<div class="ds-card" style="text-align: center; padding: 4rem 2rem;">
    <i class="bi bi-check-circle" style="font-size: 3.5rem; color: #9bdcb3; display: block; margin-bottom: 1rem;"></i>
    <h4 style="font-weight: 700; color: var(--ds-gray-800); margin-bottom: .5rem;">Semua Selesai!</h4>
    <p style="color: var(--ds-gray-400);">Tidak ada dokumen yang perlu Anda tandatangani saat ini.</p>
    <a href="{{ route('dashboard') }}" class="btn-ds-primary" style="margin-top: .5rem;">
        <i class="bi bi-grid-1x2"></i> Kembali ke Dashboard
    </a>
</div>
@else
<div style="display: flex; flex-direction: column; gap: 1rem;">
    @foreach($pendingSigners as $signer)
    <div class="ds-card">
        <div class="ds-card-body" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <div style="width: 48px; height: 48px; background: var(--ds-violet-50); border-radius: 12px;
                 display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="bi bi-file-earmark-pdf" style="font-size: 1.3rem; color: var(--ds-danger-fg);"></i>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <div style="font-weight: 700; color: var(--ds-gray-800); font-size: .95rem;">
                    {{ $signer->document->title }}
                </div>
                <div style="font-size: .78rem; color: var(--ds-gray-600); margin-top: .15rem;">
                    Diminta oleh <strong>{{ $signer->document->owner->name }}</strong>
                    · Diunggah {{ $signer->document->created_at->diffForHumans() }}
                </div>
                @if($signer->document->description)
                <div style="font-size: .75rem; color: var(--ds-gray-400); margin-top: .25rem;">
                    {{ Str::limit($signer->document->description, 80) }}
                </div>
                @endif
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: .5rem;">
                <span class="ds-badge waiting_signature"><i class="bi bi-hourglass-split"></i> Menunggu Anda</span>
                <a href="{{ route('signatures.show', $signer->document) }}" class="btn-ds-primary">
                    <i class="bi bi-pen"></i> Tanda Tangan Sekarang
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($pendingSigners->hasPages())
<div style="margin-top: 1rem; text-align: center;">
    {{ $pendingSigners->links() }}
</div>
@endif
@endif

@endsection
