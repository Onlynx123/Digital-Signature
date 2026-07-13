@extends('layouts.app')
@section('title', 'Tanda Tangan Dokumen')
@section('page-title', 'Tanda Tangan Dokumen')
@section('breadcrumb', 'Pending → Tanda tangan: ' . Str::limit($document->title, 35))

@push('styles')
<style>
.tab-btn {
    padding: .5rem 1.1rem; border-radius: 8px;
    border: 1.5px solid var(--ds-gray-200); background: #fff;
    font-size: .82rem; cursor: pointer; font-weight: 500; color: var(--ds-gray-600);
    transition: all .15s; display: inline-flex; align-items: center; gap: .4rem;
}
.tab-btn.active {
    background: var(--ds-violet-50); border-color: var(--ds-violet-600); color: var(--ds-violet-700);
}
.tab-panel { display: none; }
.tab-panel.active { display: block; }
</style>
@endpush

@section('content')

<div style="max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr 300px; gap: 1.5rem;">

    {{-- ===== LEFT: Sign Form ===== --}}
    <div>

        <form method="POST" action="{{ route('signatures.store', $document) }}"
              id="sign-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="signature_type" id="signature_type" value="canvas">
            <input type="hidden" name="signature_data" id="signature_data">

            {{-- Document info --}}
            <div class="ds-card" style="margin-bottom: 1.25rem;">
                <div class="ds-card-body">
                    <div style="display: flex; align-items: center; gap: .75rem;">
                        <div style="width: 44px; height: 44px; background: var(--ds-danger-bg); border-radius: 10px;
                             display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-file-earmark-pdf" style="font-size: 1.2rem; color: var(--ds-danger-fg);"></i>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: var(--ds-gray-800); font-size: .95rem;">{{ $document->title }}</div>
                            <div style="font-size: .75rem; color: var(--ds-gray-400);">Diunggah oleh {{ $document->owner->name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PDF Preview --}}
<div class="ds-card" style="margin-bottom:1.25rem;">
    <div class="ds-card-header">
        <h5>
            <i class="bi bi-file-earmark-pdf text-danger"></i>
            Preview Dokumen
        </h5>
    </div>

    <div class="ds-card-body">

        <iframe
            src="{{ $pdfUrl }}"
            width="100%"
            height="700"
            style="border:1px solid #ddd;border-radius:8px;">
        </iframe>

    </div>
</div>

            {{-- Signature tabs --}}
            <div class="ds-card" style="margin-bottom: 1.25rem;">
                <div class="ds-card-header">
                    <h5><i class="bi bi-pen" style="color: var(--ds-violet-600);"></i> Buat Tanda Tangan</h5>
                </div>
                <div class="ds-card-body">

                    {{-- Tab buttons --}}
                    <div style="display: flex; gap: .5rem; margin-bottom: 1.25rem;">
                        <button type="button" class="tab-btn active" id="tab-canvas"
                                onclick="switchTab('canvas')">
                            <i class="bi bi-brush"></i> Gambar Langsung
                        </button>
                        <button type="button" class="tab-btn" id="tab-upload"
                                onclick="switchTab('upload')">
                            <i class="bi bi-upload"></i> Upload Gambar
                        </button>
                    </div>

                    {{-- Canvas Tab --}}
                    <div class="tab-panel active" id="panel-canvas">
                        <div style="margin-bottom: .5rem; font-size: .78rem; color: var(--ds-gray-600);">
                            <i class="bi bi-info-circle"></i>
                            Gambar tanda tangan Anda menggunakan mouse atau layar sentuh
                        </div>
                        <canvas id="signature-canvas" width="600" height="200"></canvas>

                        <div style="display: flex; gap: .5rem; margin-top: .75rem; align-items: center;">
                            <button type="button" class="btn-ds-outline" id="clear-canvas"
                                    style="padding: .4rem .85rem; font-size: .78rem;">
                                <i class="bi bi-eraser"></i> Hapus
                            </button>
                            <span style="font-size: .72rem; color: var(--ds-gray-400); margin-left: .5rem;">
                                Tip: Gambar pelan untuk hasil yang rapi
                            </span>
                            <div id="canvas-preview" style="margin-left: auto; display: none;">
                                <span style="font-size: .72rem; color: var(--ds-success-fg); font-weight: 500;">
                                    <i class="bi bi-check-circle-fill"></i> Tanda tangan siap
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Upload Tab --}}
                    <div class="tab-panel" id="panel-upload">
                        <div class="upload-zone" onclick="document.getElementById('sig-file').click()" style="padding: 1.5rem;">
                            <div class="upload-icon" style="font-size: 1.5rem;"><i class="bi bi-image"></i></div>
                            <div class="upload-text">Klik untuk upload gambar tanda tangan</div>
                            <div class="upload-hint">Format: PNG, JPG · Maks 2 MB · Background transparan lebih bagus</div>
                        </div>
                        <input type="file" id="sig-file" name="signature_file"
                               accept="image/png,image/jpeg,image/jpg" style="display: none;">
                        <div id="sig-file-preview" style="display: none; margin-top: .75rem; padding: .75rem;
                             background: var(--ds-violet-50); border-radius: 8px; text-align: center;">
                            <img id="sig-preview-img" src="" alt="" style="max-height: 80px; max-width: 100%;">
                            <div id="sig-preview-name" style="font-size: .72rem; color: var(--ds-gray-600); margin-top: .4rem;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="ds-card">
                <div class="ds-card-body">
                    <div class="ds-alert warning" style="margin-bottom: 1rem;">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                        <div>
                            <strong>Perhatian:</strong> Dengan menandatangani, Anda menyetujui konten
                            dokumen ini. Tanda tangan bersifat final dan tidak dapat diubah.
                        </div>
                    </div>
                    <div style="display: flex; gap: .75rem; justify-content: flex-end;">
                        <a href="{{ route('signatures.pending') }}" class="btn-ds-outline">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn-ds-success" id="submit-sign" style="padding: .65rem 1.75rem; font-size: .95rem;">
                            <i class="bi bi-check-circle"></i> Konfirmasi Tanda Tangan
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

    {{-- ===== RIGHT: Document Info ===== --}}
    <div>

        {{-- Position info --}}
        @if($position)
        <div class="ds-card" style="margin-bottom: 1rem;">
            <div class="ds-card-header">
                <h5><i class="bi bi-geo-alt" style="color: var(--ds-violet-600);"></i> Posisi Tanda Tangan</h5>
            </div>
            <div class="ds-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .5rem; text-align: center;">
                    <div style="background: var(--ds-violet-50); border-radius: 8px; padding: .75rem;">
                        <div style="font-size: .65rem; color: var(--ds-gray-600); font-weight: 600; text-transform: uppercase;">Halaman</div>
                        <div style="font-size: 1.4rem; font-weight: 700; color: var(--ds-violet-700);">{{ $position->page_number }}</div>
                    </div>
                    <div style="background: var(--ds-violet-50); border-radius: 8px; padding: .75rem;">
                        <div style="font-size: .65rem; color: var(--ds-gray-600); font-weight: 600; text-transform: uppercase;">X</div>
                        <div style="font-size: 1.4rem; font-weight: 700; color: var(--ds-violet-700);">{{ $position->x_position }}</div>
                    </div>
                    <div style="background: var(--ds-violet-50); border-radius: 8px; padding: .75rem;">
                        <div style="font-size: .65rem; color: var(--ds-gray-600); font-weight: 600; text-transform: uppercase;">Y</div>
                        <div style="font-size: 1.4rem; font-weight: 700; color: var(--ds-violet-700);">{{ $position->y_position }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Hash verification --}}
        <div class="ds-card" style="margin-bottom: 1rem;">
            <div class="ds-card-header">
                <h5><i class="bi bi-shield-check" style="color: var(--ds-success-fg);"></i> Integritas Dokumen</h5>
            </div>
            <div class="ds-card-body">
                <div class="ds-alert" style="background: var(--ds-success-bg); border-color: #bfe5cd; color: var(--ds-success-fg); margin-bottom: .75rem;">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    <div>Dokumen terverifikasi. Hash cocok dengan yang tersimpan.</div>
                </div>
                <div class="hash-box">{{ substr($document->hash_value, 0, 32) }}...</div>
            </div>
        </div>

        {{-- Progress status --}}
        <div class="ds-card">
            <div class="ds-card-header">
                <h5><i class="bi bi-people" style="color: var(--ds-violet-600);"></i> Status Signer Lain</h5>
            </div>
            <div style="padding: .5rem 1rem;">
                @foreach($document->signers as $s)
                <div style="display: flex; align-items: center; gap: .6rem; padding: .5rem 0; border-bottom: 1px solid var(--ds-gray-100);">
                    <div style="width: 28px; height: 28px; border-radius: 50%;
                         background: {{ $s->status === 'signed' ? 'var(--ds-success-bg)' : 'var(--ds-gray-100)' }};
                         display: flex; align-items: center; justify-content: center;
                         font-size: .65rem; font-weight: 700;
                         color: {{ $s->status === 'signed' ? 'var(--ds-success-fg)' : 'var(--ds-gray-400)' }};">
                        {{ strtoupper(substr($s->signer->name, 0, 2)) }}
                    </div>
                    <div style="flex: 1; font-size: .8rem; color: var(--ds-gray-800);">{{ $s->signer->name }}</div>
                    @if($s->signer_id === auth()->id())
                        <span style="font-size: .68rem; background: var(--ds-violet-100); color: var(--ds-violet-700); padding: 2px 8px; border-radius: 10px; font-weight: 600;">Anda</span>
                    @else
                        <span class="ds-badge {{ $s->status }}" style="font-size: .68rem; padding: 2px 8px;">
                            <i class="bi {{ $s->status === 'signed' ? 'bi-check-circle-fill' : 'bi-hourglass-split' }}"></i>
                        </span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
// ===== TAB SWITCHER =====
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById('panel-' + tab).classList.add('active');
    document.getElementById('signature_type').value = tab;
}

// ===== CANVAS SIGNATURE =====
const canvas = document.getElementById('signature-canvas');
const ctx    = canvas.getContext('2d');
let drawing  = false;
let hasDrawn = false;

// Set canvas style
ctx.strokeStyle = 'var(--ds-gray-800)';
ctx.lineWidth   = 2.5;
ctx.lineCap     = 'round';
ctx.lineJoin    = 'round';

function getPos(e) {
    const rect  = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const src   = e.touches ? e.touches[0] : e;
    return {
        x: (src.clientX - rect.left) * scaleX,
        y: (src.clientY - rect.top)  * scaleY,
    };
}

canvas.addEventListener('mousedown',  e => { drawing = true; hasDrawn = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); });
canvas.addEventListener('mousemove',  e => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
canvas.addEventListener('mouseup',    () => { drawing = false; checkCanvasReady(); });
canvas.addEventListener('mouseleave', () => { drawing = false; });
canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; hasDrawn = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); }, { passive: false });
canvas.addEventListener('touchmove',  e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }, { passive: false });
canvas.addEventListener('touchend',   () => { drawing = false; checkCanvasReady(); });

function checkCanvasReady() {
    if (hasDrawn) document.getElementById('canvas-preview').style.display = 'block';
}

document.getElementById('clear-canvas').addEventListener('click', () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasDrawn = false;
    document.getElementById('canvas-preview').style.display = 'none';
});

// ===== UPLOAD FILE PREVIEW =====
document.getElementById('sig-file').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('sig-preview-img').src = e.target.result;
        document.getElementById('sig-preview-name').textContent = file.name;
        document.getElementById('sig-file-preview').style.display = 'block';
    };
    reader.readAsDataURL(file);
});

// ===== FORM SUBMIT =====
document.getElementById('sign-form').addEventListener('submit', function(e) {
    const type = document.getElementById('signature_type').value;

    if (type === 'canvas') {
        if (!hasDrawn) {
            e.preventDefault();
            alert('Silakan buat tanda tangan terlebih dahulu!');
            return;
        }
        document.getElementById('signature_data').value = canvas.toDataURL('image/png');
    } else {
        const fileInput = document.getElementById('sig-file');
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Silakan upload gambar tanda tangan!');
            return;
        }
    }

    const btn = document.getElementById('submit-sign');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
    btn.disabled = true;
});
</script>
@endpush
