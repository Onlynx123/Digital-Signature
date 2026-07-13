@extends('layouts.app')
@section('title', 'Daftar Dokumen')
@section('page-title', 'Daftar Dokumen')
@section('breadcrumb', 'Kelola semua dokumen Anda')

@section('topbar-actions')
    <a href="{{ route('documents.create') }}" class="btn-ds-primary">
        <i class="bi bi-plus-lg"></i> Upload Dokumen
    </a>
@endsection

@section('content')

{{-- Filter / Search bar --}}
<div class="ds-card" style="margin-bottom: 1.25rem;">
    <div class="ds-card-body" style="padding: .875rem 1.25rem;">
        <form method="GET" style="display: flex; gap: .75rem; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" class="ds-input" value="{{ request('search') }}"
                   placeholder="Cari judul dokumen..." style="max-width: 300px; flex: 1;">

            <select name="status" class="ds-select" style="width: auto;">
                <option value="">Semua Status</option>
                <option value="draft"             {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="waiting_signature" {{ request('status') === 'waiting_signature' ? 'selected' : '' }}>Menunggu Tanda Tangan</option>
                <option value="completed"         {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="locked"            {{ request('status') === 'locked' ? 'selected' : '' }}>Terkunci</option>
            </select>

            <button type="submit" class="btn-ds-outline">
                <i class="bi bi-search"></i> Filter
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('documents.index') }}" class="btn-ds-outline" style="color: var(--ds-gray-400); border-color: var(--ds-gray-200);">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Documents Table --}}
<div class="ds-card">
    <div class="ds-card-header">
        <h5><i class="bi bi-file-earmark-pdf" style="color: var(--ds-violet-600);"></i> Dokumen
            <span style="font-size: .78rem; font-weight: 400; color: var(--ds-gray-400); margin-left: .25rem;">({{ $documents->total() }} total)</span>
        </h5>
    </div>

    @if($documents->count() === 0)
        <div style="text-align: center; padding: 4rem 2rem; color: var(--ds-gray-400);">
            <i class="bi bi-folder-x" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
            <div style="font-size: .95rem; font-weight: 500; margin-bottom: .5rem;">Belum ada dokumen</div>
            <div style="font-size: .8rem; margin-bottom: 1.25rem;">Upload dokumen PDF Anda untuk mulai proses tanda tangan</div>
            <a href="{{ route('documents.create') }}" class="btn-ds-primary">
                <i class="bi bi-plus-lg"></i> Upload Dokumen Pertama
            </a>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="ds-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Judul</th>
                        <th>Status</th>
                        <th>Signer</th>
                        <th>Tanggal Upload</th>
                        @if(auth()->user()->isAdmin())
                            <th>Pemilik</th>
                        @endif
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                    <tr>
                        <td>
                            <a href="{{ route('documents.show', $doc) }}" class="doc-title-link">
                                <i class="bi bi-file-earmark-pdf" style="color: var(--ds-danger-fg); margin-right: .3rem;"></i>
                                {{ $doc->title }}
                            </a>
                            @if($doc->description)
                                <div style="font-size: .72rem; color: var(--ds-gray-400); margin-top: 2px;">
                                    {{ Str::limit($doc->description, 60) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="ds-badge {{ $doc->status }}">
                                @switch($doc->status)
                                    @case('draft')             <i class="bi bi-pencil"></i> Draft @break
                                    @case('waiting_signature') <i class="bi bi-hourglass-split"></i> Menunggu @break
                                    @case('completed')         <i class="bi bi-check-circle"></i> Selesai @break
                                    @case('locked')            <i class="bi bi-lock-fill"></i> Terkunci @break
                                @endswitch
                            </span>
                        </td>
                        <td>
                            @php
                                $total  = $doc->signers->count();
                                $signed = $doc->signers->where('status', 'signed')->count();
                            @endphp
                            <span style="font-size: .82rem;">
                                <span style="color: var(--ds-success-fg); font-weight: 600;">{{ $signed }}</span>
                                <span style="color: var(--ds-gray-400);">/ {{ $total }}</span>
                                <span style="color: var(--ds-gray-400); font-size: .72rem;"> signed</span>
                            </span>
                        </td>
                        <td style="font-size: .8rem; color: var(--ds-gray-400); white-space: nowrap;">
                            {{ $doc->created_at->format('d M Y') }}<br>
                            <span style="font-size: .7rem;">{{ $doc->created_at->format('H:i') }}</span>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td style="font-size: .8rem;">{{ $doc->owner->name }}</td>
                        @endif
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('documents.show', $doc) }}" class="btn-ds-outline" style="padding: .35rem .75rem; font-size: .75rem;">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            @if(!$doc->isLocked() && ($doc->user_id === auth()->id() || auth()->user()->isAdmin()))
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}" style="display: inline;"
                                  onsubmit="return confirm('Yakin hapus dokumen ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-ds-danger" style="padding: .35rem .65rem; font-size: .75rem; margin-left: .25rem;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($documents->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--ds-gray-100); display: flex; justify-content: center; gap: .5rem;">
            {{ $documents->withQueryString()->links('vendor.pagination.simple-tailwind') }}
        </div>
        @endif
    @endif
</div>

@endsection
