@extends('layouts.app')
@section('title', $document->title)
@section('page-title', 'Detail Dokumen')
@section('breadcrumb', 'Dokumen → ' . Str::limit($document->title, 40))

@section('topbar-actions')
    <a href="{{ route('documents.download', $document) }}" class="btn-ds-outline">
        <i class="bi bi-download"></i> Download PDF
    </a>
    <a href="{{ route('documents.index') }}" class="btn-ds-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')

<div style="display: grid; grid-template-columns: 1fr 320px; gap: 1.5rem;">

    {{-- LEFT: Main Info --}}
    <div>

        {{-- Document Header Card --}}
        <div class="ds-card" style="margin-bottom: 1.25rem;">
            <div class="ds-card-body">
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="width: 56px; height: 56px; background: var(--ds-danger-bg); border-radius: 12px;
                         display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-file-earmark-pdf" style="font-size: 1.6rem; color: var(--ds-danger-fg);"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; margin-bottom: .25rem;">
                            <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--ds-gray-800);">
                                {{ $document->title }}
                            </h4>
                            <span class="ds-badge {{ $document->status }}">
                                @switch($document->status)
                                    @case('draft')             <i class="bi bi-pencil"></i> Draft @break
                                    @case('waiting_signature') <i class="bi bi-hourglass-split"></i> Menunggu Tanda Tangan @break
                                    @case('completed')         <i class="bi bi-check-circle"></i> Selesai @break
                                    @case('locked')            <i class="bi bi-lock-fill"></i> Terkunci @break
                                @endswitch
                            </span>
                        </div>
                        @if($document->description)
                            <p style="margin: .25rem 0 0; color: var(--ds-gray-600); font-size: .875rem;">
                                {{ $document->description }}
                            </p>
                        @endif
                        <div style="margin-top: .5rem; font-size: .75rem; color: var(--ds-gray-400);">
                            Diunggah oleh <strong>{{ $document->owner->name }}</strong>
                            · {{ $document->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hash Info --}}
        <div class="ds-card" style="margin-bottom: 1.25rem;">
            <div class="ds-card-header">
                <h5><i class="bi bi-fingerprint" style="color: var(--ds-violet-600);"></i> Integritas Dokumen (SHA-256)</h5>
            </div>
            <div class="ds-card-body">
                <div class="ds-alert info" style="margin-bottom: .75rem;">
                    <i class="bi bi-shield-check-fill flex-shrink-0"></i>
                    <div>Hash ini digunakan untuk memverifikasi bahwa dokumen tidak dimodifikasi setelah diunggah.</div>
                </div>
                <div class="hash-box">{{ $document->hash_value }}</div>
                <div style="margin-top: .5rem; text-align: right;">
                    <a href="{{ route('verify.index') }}" style="font-size: .75rem; color: var(--ds-violet-600); text-decoration: none;">
                        <i class="bi bi-shield-check"></i> Verifikasi dokumen ini
                    </a>
                </div>
            </div>
        </div>

        {{-- Signatures Table --}}
        <div class="ds-card">
            <div class="ds-card-header">
                <h5><i class="bi bi-pen" style="color: var(--ds-violet-600);"></i> Daftar Tanda Tangan</h5>
            </div>

            @if($document->signers->count() === 0)
                <div style="padding: 2rem; text-align: center; color: var(--ds-gray-400); font-size: .875rem;">
                    Belum ada signer ditambahkan
                </div>
            @else
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Signer</th>
                            <th>Posisi</th>
                            <th>Status</th>
                            <th>Waktu Tanda Tangan</th>
                            <th>Tanda Tangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($document->signers as $signer)
                        @php
                            $position  = $document->signaturePositions->where('signer_id', $signer->signer_id)->first();
                            $signature = $document->signatures->where('signer_id', $signer->signer_id)->first();
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: .6rem;">
                                    <div style="width: 32px; height: 32px; background: var(--ds-violet-100); border-radius: 50%;
                                         display: flex; align-items: center; justify-content: center;
                                         font-size: .72rem; font-weight: 700; color: var(--ds-violet-800);">
                                        {{ strtoupper(substr($signer->signer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 500; font-size: .875rem;">{{ $signer->signer->name }}</div>
                                        <div style="font-size: .7rem; color: var(--ds-gray-400);">{{ $signer->signer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size: .78rem; color: var(--ds-gray-600);">
                                @if($position)
                                    Hal. {{ $position->page_number }}<br>
                                    <span style="color: var(--ds-gray-400);">X:{{ $position->x_position }} Y:{{ $position->y_position }}</span>
                                @else
                                    <span style="color: var(--ds-gray-400);">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="ds-badge {{ $signer->status }}">
                                    <i class="bi {{ $signer->status === 'signed' ? 'bi-check-circle-fill' : 'bi-hourglass-split' }}"></i>
                                    {{ $signer->status === 'signed' ? 'Sudah' : 'Belum' }}
                                </span>
                            </td>
                            <td style="font-size: .78rem; color: var(--ds-gray-600);">
                                @if($signature)
                                    {{ $signature->signed_at->format('d M Y') }}<br>
                                    <span style="color: var(--ds-gray-400);">{{ $signature->signed_at->format('H:i') }}</span>
                                @else
                                    <span style="color: var(--ds-gray-400);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($signature)
                                    <img src="{{ route('signatures.image', $signature->id) }}"
                                         alt="Tanda tangan {{ $signer->signer->name }}"
                                         style="max-height: 40px; max-width: 120px; object-fit: contain; border: 1px solid var(--ds-gray-200); border-radius: 4px; padding: 2px;">
                                @else
                                    @if($signer->signer_id === auth()->id() && !$document->isLocked())
                                        <a href="{{ route('signatures.show', $document) }}" class="btn-ds-primary" style="padding: .35rem .75rem; font-size: .75rem;">
                                            <i class="bi bi-pen"></i> Tanda Tangani
                                        </a>
                                    @else
                                        <span style="color: var(--ds-gray-400); font-size: .78rem;">Belum tanda tangan</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- RIGHT: Sidebar Info --}}
    <div>

        {{-- Progress --}}
        <div class="ds-card" style="margin-bottom: 1rem;">
            <div class="ds-card-header">
                <h5><i class="bi bi-bar-chart" style="color: var(--ds-violet-600);"></i> Progress</h5>
            </div>
            <div class="ds-card-body">
                @php
                    $total  = $document->signers->count();
                    $signed = $document->signers->where('status', 'signed')->count();
                    $pct    = $total > 0 ? round(($signed / $total) * 100) : 0;
                @endphp
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: .5rem;">
                    <span style="font-size: .82rem; color: var(--ds-gray-600);">Tanda tangan</span>
                    <span style="font-size: .875rem; font-weight: 700; color: var(--ds-gray-800);">{{ $signed }}/{{ $total }}</span>
                </div>
                <div style="background: var(--ds-gray-200); border-radius: 99px; height: 8px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--ds-violet-600), var(--ds-violet-400)); height: 100%;
                         width: {{ $pct }}%; border-radius: 99px; transition: width .5s ease;"></div>
                </div>
                <div style="text-align: center; margin-top: .4rem; font-size: .72rem; color: var(--ds-gray-400);">{{ $pct }}% selesai</div>

                @if($document->isLocked())
                <div class="ds-alert info" style="margin-top: 1rem; margin-bottom: 0;">
                    <i class="bi bi-lock-fill flex-shrink-0"></i>
                    <div>Dokumen terkunci. Tidak ada perubahan lebih lanjut.</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="ds-card" style="margin-bottom: 1rem;">
            <div class="ds-card-header">
                <h5><i class="bi bi-lightning" style="color: var(--ds-violet-600);"></i> Aksi Cepat</h5>
            </div>
            <div class="ds-card-body" style="display: flex; flex-direction: column; gap: .5rem;">
                <a href="{{ route('documents.download', $document) }}" class="btn-ds-outline" style="justify-content: center;">
                    <i class="bi bi-download"></i> Download PDF
                </a>
                <a href="{{ route('verify.index') }}" class="btn-ds-outline" style="justify-content: center;">
                    <i class="bi bi-shield-check"></i> Verifikasi Dokumen
                </a>
                @if(!$document->isLocked() && $document->user_id === auth()->id())
                <form method="POST" action="{{ route('documents.destroy', $document) }}"
                      onsubmit="return confirm('Hapus dokumen ini secara permanen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-ds-danger" style="width: 100%; justify-content: center;">
                        <i class="bi bi-trash"></i> Hapus Dokumen
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Audit trail for this doc --}}
        <div class="ds-card">
            <div class="ds-card-header">
                <h5><i class="bi bi-journal" style="color: var(--ds-violet-600);"></i> Riwayat Dokumen</h5>
            </div>
            <div style="padding: .75rem 1.25rem;">
                <div class="audit-row">
                    <div class="audit-dot"></div>
                    <div>
                        <div class="audit-action">UPLOAD_DOCUMENT</div>
                        <div class="audit-desc">Dokumen diunggah</div>
                        <div class="audit-time">{{ $document->created_at->diffForHumans() }} · {{ $document->owner->name }}</div>
                    </div>
                </div>
                @foreach($document->signatures->sortBy('signed_at') as $sig)
                <div class="audit-row">
                    <div class="audit-dot" style="background: var(--ds-success-fg);"></div>
                    <div>
                        <div class="audit-action" style="color: var(--ds-success-fg);">SIGN_DOCUMENT</div>
                        <div class="audit-desc">Ditandatangani oleh {{ $sig->signer->name }}</div>
                        <div class="audit-time">{{ $sig->signed_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
                @if($document->isLocked())
                <div class="audit-row" style="border: none;">
                    <div class="audit-dot" style="background: var(--ds-violet-700);"></div>
                    <div>
                        <div class="audit-action" style="color: var(--ds-violet-700);">DOCUMENT_LOCKED</div>
                        <div class="audit-desc">Dokumen dikunci otomatis</div>
                        <div class="audit-time">Semua signer selesai</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
