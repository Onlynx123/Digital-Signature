@extends('layouts.app')
@section('title', 'Verifikasi Dokumen')
@section('page-title', 'Verifikasi Keaslian Dokumen')
@section('breadcrumb', 'Cek apakah dokumen telah dimodifikasi')

@section('content')

<div style="max-width: 680px; margin: 0 auto;">

    <div class="ds-card" style="margin-bottom: 1.5rem;">
        <div class="ds-card-header">
            <h5><i class="bi bi-shield-check" style="color: var(--ds-violet-600);"></i> Upload Dokumen untuk Diverifikasi</h5>
        </div>
        <div class="ds-card-body">

            <div class="ds-alert info" style="margin-bottom: 1.25rem;">
                <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                <div>
                    Sistem akan menghitung <strong>hash SHA-256</strong> dari file yang Anda upload,
                    kemudian membandingkan dengan hash yang tersimpan di database saat dokumen pertama kali diunggah.
                </div>
            </div>

            <form method="POST" action="{{ route('verify.check') }}" enctype="multipart/form-data" id="verify-form">
                @csrf
                <div style="margin-bottom: 1.25rem;">
                    <label class="ds-label">File PDF yang akan diverifikasi</label>
                    <div class="upload-zone" onclick="document.getElementById('verify-file').click()" id="verify-zone">
                        <div class="upload-icon"><i class="bi bi-shield-check"></i></div>
                        <div class="upload-text" id="verify-upload-text">
                            <strong style="color: var(--ds-violet-600);">Klik untuk pilih file PDF</strong>
                        </div>
                        <div class="upload-hint">Sistem akan otomatis menghitung hash dan membandingkan</div>
                    </div>
                    <input type="file" id="verify-file" name="file" accept="application/pdf" style="display: none;" required>
                    @error('file') <div class="ds-error" style="margin-top: .3rem;">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn-ds-primary" id="verify-btn" style="width: 100%; justify-content: center; padding: .7rem;">
                    <i class="bi bi-search"></i> Verifikasi Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- ===== RESULT ===== --}}
    @isset($result)

        @if($result['valid'])
        <div class="verify-result valid">
            <span class="result-icon"><i class="bi bi-check-lg"></i></span>
            <div class="result-title" style="color: var(--ds-success-fg);">DOKUMEN VALID</div>
            <p style="color: var(--ds-success-fg); font-size: .9rem; margin-bottom: 1.25rem;">
                Dokumen ini <strong>asli</strong> dan <strong>belum dimodifikasi</strong>
                sejak pertama kali diunggah ke sistem.
            </p>

            <div style="background: rgba(255,255,255,.6); border-radius: 10px; padding: 1rem; text-align: left;">
                <div style="display: grid; grid-template-columns: auto 1fr; gap: .4rem .75rem; font-size: .82rem;">
                    <span style="color: var(--ds-success-fg); font-weight: 600;">Judul:</span>
                    <span style="color: var(--ds-gray-800);">{{ $result['document']->title }}</span>

                    <span style="color: var(--ds-success-fg); font-weight: 600;">Pemilik:</span>
                    <span style="color: var(--ds-gray-800);">{{ $result['document']->owner->name }}</span>

                    <span style="color: var(--ds-success-fg); font-weight: 600;">Status:</span>
                    <span><span class="ds-badge {{ $result['document']->status }}">{{ $result['document']->status }}</span></span>

                    <span style="color: var(--ds-success-fg); font-weight: 600;">Diunggah:</span>
                    <span style="color: var(--ds-gray-800);">{{ $result['document']->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div style="margin-top: .75rem; padding-top: .75rem; border-top: 1px solid rgba(134,239,172,.4);">
                    <div style="font-size: .7rem; color: var(--ds-success-fg); font-weight: 600; margin-bottom: .3rem;">HASH SHA-256:</div>
                    <div class="hash-box" style="background: rgba(255,255,255,.5); border-color: #9bdcb3;">{{ $result['hash'] }}</div>
                </div>
            </div>
        </div>

        @else
        <div class="verify-result invalid">
            <span class="result-icon"><i class="bi bi-x-lg"></i></span>
            <div class="result-title" style="color: var(--ds-danger-fg);">DOKUMEN TELAH DIMODIFIKASI</div>
            <p style="color: var(--ds-danger-fg); font-size: .9rem; margin-bottom: 1.25rem;">
                Hash dokumen <strong>tidak cocok</strong> dengan yang tersimpan di database.
                Dokumen kemungkinan telah diubah setelah proses tanda tangan.
            </p>

            <div style="background: rgba(255,255,255,.6); border-radius: 10px; padding: 1rem; text-align: left;">
                <div style="font-size: .7rem; color: var(--ds-danger-fg); font-weight: 600; margin-bottom: .3rem;">HASH DARI FILE YANG DIUPLOAD:</div>
                <div class="hash-box" style="background: rgba(255,255,255,.5); border-color: #eaadad;">{{ $result['hash'] }}</div>
                <div style="margin-top: .75rem; font-size: .78rem; color: var(--ds-danger-fg);">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Hash ini tidak ditemukan dalam database sistem.
                </div>
            </div>
        </div>
        @endif

    @endisset

    {{-- Info cards below --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem;">
        <div class="ds-card">
            <div class="ds-card-body" style="text-align: center; padding: 1.25rem;">
                <i class="bi bi-fingerprint" style="font-size: 1.75rem; color: var(--ds-violet-600); display: block; margin-bottom: .5rem;"></i>
                <div style="font-weight: 600; font-size: .875rem; color: var(--ds-gray-800); margin-bottom: .25rem;">Hash SHA-256</div>
                <div style="font-size: .75rem; color: var(--ds-gray-400);">Setiap file menghasilkan hash unik. Perubahan sekecil apapun akan menghasilkan hash yang berbeda.</div>
            </div>
        </div>
        <div class="ds-card">
            <div class="ds-card-body" style="text-align: center; padding: 1.25rem;">
                <i class="bi bi-shield-check" style="font-size: 1.75rem; color: var(--ds-success-fg); display: block; margin-bottom: .5rem;"></i>
                <div style="font-weight: 600; font-size: .875rem; color: var(--ds-gray-800); margin-bottom: .25rem;">Integritas Terjamin</div>
                <div style="font-size: .75rem; color: var(--ds-gray-400);">Dokumen yang VALID membuktikan isinya sama persis dengan yang ditandatangani semua pihak.</div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.getElementById('verify-file').addEventListener('change', function() {
    if (this.files.length > 0) {
        const name = this.files[0].name;
        document.getElementById('verify-upload-text').innerHTML =
            `<strong style="color: var(--ds-success-fg);"><i class="bi bi-check-circle"></i> ${name}</strong>`;
    }
});

document.getElementById('verify-form').addEventListener('submit', function() {
    const btn = document.getElementById('verify-btn');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memverifikasi...';
    btn.disabled = true;
});
</script>
@endpush
