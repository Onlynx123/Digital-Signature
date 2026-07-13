@extends('layouts.app')
@section('title', 'Upload Dokumen')
@section('page-title', 'Upload Dokumen')
@section('breadcrumb', 'Dokumen → Upload baru')

@section('content')

<div style="max-width: 1200px; margin: 0 auto;">

    {{-- Step indicator --}}
    <div class="steps-bar" style="margin-bottom: 2rem;">
        <div class="step-item" style="flex-direction: column; align-items: center; flex: 0 0 auto; min-width: 80px;">
            <div class="step-bubble active" id="step-bubble-1">1</div>
            <div class="step-label active" id="step-label-1">Info Dokumen</div>
        </div>
        <div class="step-connector" id="connector-1"></div>
        <div class="step-item" style="flex-direction: column; align-items: center; flex: 0 0 auto; min-width: 80px;">
            <div class="step-bubble" id="step-bubble-2">2</div>
            <div class="step-label" id="step-label-2">Tambah Signer</div>
        </div>
        <div class="step-connector" id="connector-2"></div>
        <div class="step-item" style="flex-direction: column; align-items: center; flex: 0 0 auto; min-width: 80px;">
            <div class="step-bubble" id="step-bubble-3">3</div>
            <div class="step-label" id="step-label-3">Kirim</div>
        </div>
    </div>

    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" id="upload-form">
        @csrf

        {{-- ===== STEP 1: Info Dokumen ===== --}}
        <div id="step-1" class="ds-card" style="margin-bottom: 1.25rem;">
            <div class="ds-card-header">
                <h5><i class="bi bi-info-circle" style="color: var(--ds-violet-600);"></i> Step 1 — Informasi Dokumen</h5>
            </div>
            <div class="ds-card-body">

                <div style="margin-bottom: 1rem;">
                    <label class="ds-label" for="title">Judul Dokumen <span style="color: var(--ds-danger-fg);">*</span></label>
                    <input type="text" id="title" name="title" class="ds-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
                           value="{{ old('title') }}" placeholder="cth: NDA Partner Q4 2024" required>
                    @error('title') <div class="ds-error">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="ds-label" for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="ds-textarea"
                              rows="3" placeholder="Keterangan singkat tentang dokumen ini (opsional)">{{ old('description') }}</textarea>
                    @error('description') <div class="ds-error">{{ $message }}</div> @enderror
                </div>

                {{-- File Upload Zone --}}
                <div>
                    <label class="ds-label">File PDF <span style="color: var(--ds-danger-fg);">*</span></label>
                    <div class="upload-zone" id="upload-zone" onclick="document.getElementById('file-input').click()">
                        <div class="upload-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
                        <div class="upload-text" id="upload-text">
                            <strong style="color: var(--ds-violet-600);">Klik untuk pilih file</strong> atau drag &amp; drop di sini
                        </div>
                        <div class="upload-hint">Hanya file PDF · Maksimal 20 MB</div>
                    </div>
                    <input type="file" id="file-input" name="file" accept="application/pdf" style="display: none;" required>
                    @error('file') <div class="ds-error" style="margin-top: .3rem;">{{ $message }}</div> @enderror

                    {{-- File preview --}}
                    <div id="file-preview" style="display: none; margin-top: .75rem; padding: .75rem 1rem;
                         background: var(--ds-violet-50); border: 1px solid var(--ds-violet-100); border-radius: 8px;
                         display: none; align-items: center; gap: .75rem;">
                        <i class="bi bi-file-earmark-pdf" style="font-size: 1.5rem; color: var(--ds-danger-fg);"></i>
                        <div style="flex: 1;">
                            <div id="file-name" style="font-size: .85rem; font-weight: 600; color: var(--ds-violet-800);"></div>
                            <div id="file-size" style="font-size: .72rem; color: var(--ds-violet-400);"></div>
                        </div>
                        <button type="button" onclick="clearFile()" style="background: none; border: none; cursor: pointer; color: var(--ds-gray-400); font-size: 1.1rem;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== STEP 2: Signers with PDF Preview ===== --}}
        <div id="step-2" class="ds-card" style="margin-bottom: 1.25rem;">
            <div class="ds-card-header">
                <h5><i class="bi bi-people" style="color: var(--ds-violet-600);"></i> Step 2 — Tambah Signer</h5>
                <span style="font-size: .75rem; color: var(--ds-gray-400);">Minimal 1 signer</span>
            </div>
            <div class="ds-card-body">

                {{-- Two-column layout: Left for signer selection, Right for PDF preview --}}
                <div style="display: grid; grid-template-columns: 350px 1fr; gap: 1.5rem; margin-bottom: 1rem;">

                    {{-- LEFT COLUMN: Signer Selection --}}
                    <div>
                        <div style="margin-bottom: 1rem;">
                            <label class="ds-label" for="signer-select">Pilih Penandatangan</label>
                            <select id="signer-select" class="ds-select">
                                <option value="">-- Pilih user --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div style="font-size: .75rem; color: var(--ds-gray-400); margin-top: .35rem;">
                                Pilih signer terlebih dahulu
                            </div>
                        </div>

                        {{-- PDF Status --}}
                        <div id="pdf-status-empty" style="padding: 1rem; background: var(--ds-paper); border-radius: 8px;
                             text-align: center; color: var(--ds-gray-400); font-size: .82rem;">
                            <i class="bi bi-file-pdf" style="font-size: 1.5rem; display: block; margin-bottom: .4rem; color: var(--ds-violet-100);"></i>
                            Upload PDF terlebih dahulu
                        </div>

                        <div id="pdf-status-ready" style="display: none; padding: 1rem; background: var(--ds-success-50); border: 1px solid var(--ds-success-100); border-radius: 8px; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: center; gap: .5rem; margin-bottom: .5rem;">
                                <i class="bi bi-check-circle" style="color: var(--ds-success-fg);"></i>
                                <strong style="color: var(--ds-success-fg); font-size: .85rem;">PDF Ready</strong>
                            </div>
                            <div style="font-size: .75rem; color: var(--ds-gray-600);">
                                <span id="pdf-page-count">0</span> halaman
                            </div>
                        </div>

                        {{-- Position Selected Info --}}
                        <div id="position-info" style="display: none; padding: 1rem; background: var(--ds-violet-50); border: 1px solid var(--ds-violet-200); border-radius: 8px; margin-bottom: 1rem;">
                            <div style="display: flex; align-items: flex-start; gap: .5rem; margin-bottom: .5rem;">
                                <i class="bi bi-geo-alt-fill" style="color: var(--ds-violet-600); flex-shrink: 0;"></i>
                                <div style="flex: 1;">
                                    <strong style="color: var(--ds-violet-800); font-size: .85rem;">Position Selected</strong>
                                    <div style="font-size: .75rem; color: var(--ds-violet-600); margin-top: .25rem;">
                                        Page <span id="pos-page">0</span> · X: <span id="pos-x">0</span> Y: <span id="pos-y">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Add Button --}}
                        <button type="button" class="btn-ds-primary" id="add-signer-btn" style="width: 100%; display: none;" onclick="addSigner()">
                            <i class="bi bi-plus-lg"></i> Tambah Signer
                        </button>
                    </div>

                    {{-- RIGHT COLUMN: PDF Preview --}}
                    <div>
                        {{-- PDF Viewer Container --}}
                        <div id="pdf-container" style="display: none; border: 1px solid var(--ds-gray-100); border-radius: 8px; overflow: hidden; background: var(--ds-paper);">
                            {{-- PDF Toolbar --}}
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: .75rem 1rem; background: var(--ds-gray-50); border-bottom: 1px solid var(--ds-gray-100);">
                                <div style="display: flex; align-items: center; gap: .5rem;">
                                    <button type="button" id="pdf-prev" class="btn-ds-outline" style="padding: .4rem .7rem; font-size: .75rem;" onclick="pdfPreview.previousPage()">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <div style="padding: 0 1rem; font-size: .85rem; color: var(--ds-gray-600); min-width: 120px; text-align: center;">
                                        Page <span id="pdf-current-page">0</span> / <span id="pdf-total-pages">0</span>
                                    </div>
                                    <button type="button" id="pdf-next" class="btn-ds-outline" style="padding: .4rem .7rem; font-size: .75rem;" onclick="pdfPreview.nextPage()">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                                <div style="display: flex; align-items: center; gap: .5rem;">
                                    <button type="button" id="pdf-zoom-out" class="btn-ds-outline" style="padding: .4rem .7rem; font-size: .75rem;" onclick="pdfPreview.zoomOut()">
                                        <i class="bi bi-zoom-out"></i>
                                    </button>
                                    <div style="font-size: .75rem; color: var(--ds-gray-600); min-width: 50px; text-align: center;">
                                        <span id="pdf-zoom-level">100</span>%
                                    </div>
                                    <button type="button" id="pdf-zoom-in" class="btn-ds-outline" style="padding: .4rem .7rem; font-size: .75rem;" onclick="pdfPreview.zoomIn()">
                                        <i class="bi bi-zoom-in"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- PDF Canvas Area --}}
                            <div id="pdf-viewer" style="height: 500px; overflow: auto; position: relative; background: var(--ds-paper);">
                                <canvas id="pdf-canvas" style="display: block; margin: 0 auto; max-width: 100%;"></canvas>
                            </div>

                            {{-- Click instruction --}}
                            <div id="pdf-instruction" style="display: none; padding: .75rem 1rem; background: var(--ds-info-50); border-top: 1px solid var(--ds-info-100); text-align: center; font-size: .82rem; color: var(--ds-info-fg);">
                                <i class="bi bi-cursor-fill"></i> Klik pada PDF untuk menentukan posisi tanda tangan
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Signer list --}}
                <div id="signers-list" style="margin-top: 1.5rem;">
                    {{-- Filled by JS --}}
                </div>

                <div id="signers-empty" style="padding: 1rem; background: var(--ds-paper); border-radius: 8px;
                     text-align: center; color: var(--ds-gray-400); font-size: .82rem;">
                    <i class="bi bi-person-plus" style="font-size: 1.5rem; display: block; margin-bottom: .4rem; color: var(--ds-violet-100);"></i>
                    Belum ada signer ditambahkan. Minimal 1 signer diperlukan.
                </div>

                {{-- Hidden inputs for signers (diisi JS) --}}
                <div id="hidden-signers"></div>

                @error('signers')
                    <div class="ds-error" style="margin-top: .5rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ===== STEP 3: Submit ===== --}}
        <div id="step-3" class="ds-card" style="margin-bottom: 1.25rem;">
            <div class="ds-card-header">
                <h5><i class="bi bi-send" style="color: var(--ds-violet-600);"></i> Step 3 — Konfirmasi & Kirim</h5>
            </div>
            <div class="ds-card-body">
                <div class="ds-alert info" style="margin-bottom: 1rem;">
                    <i class="bi bi-info-circle-fill" style="flex-shrink: 0;"></i>
                    <div>
                        Setelah dokumen dikirim:
                        <ul style="margin: .25rem 0 0 0; padding-left: 1.1rem;">
                            <li>Hash SHA-256 otomatis digenerate untuk menjamin integritas dokumen</li>
                            <li>Semua signer akan mendapat notifikasi untuk menandatangani</li>
                            <li>Dokumen akan terkunci otomatis setelah semua signer selesai</li>
                        </ul>
                    </div>
                </div>

                <div style="display: flex; gap: .75rem; justify-content: flex-end;">
                    <a href="{{ route('documents.index') }}" class="btn-ds-outline">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn-ds-primary" id="submit-btn" style="padding: .65rem 1.75rem;">
                        <i class="bi bi-send"></i> Kirim Undangan Tanda Tangan
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="{{ asset('js/pdf-preview.js') }}"></script>
<script>
let signers = [];
let currentSignerData = null;

const fileInput = document.getElementById('file-input');
const uploadZone = document.getElementById('upload-zone');

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) handleFileUpload(this.files[0]);
});

uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('drag-over');
});
uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-over'));
uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') {
        fileInput.files = e.dataTransfer.files;
        handleFileUpload(file);
    } else {
        alert('Hanya file PDF yang diperbolehkan!');
    }
});

function handleFileUpload(file) {
    const mb = (file.size / 1024 / 1024).toFixed(2);
    document.getElementById('file-name').textContent = file.name;
    document.getElementById('file-size').textContent = mb + ' MB';
    document.getElementById('file-preview').style.display = 'flex';
    document.getElementById('upload-text').innerHTML =
        '<strong style="color: var(--ds-success-fg);"><i class="bi bi-check-circle"></i> File dipilih</strong>';

    const reader = new FileReader();
    reader.onload = (e) => {
        pdfPreview.loadPDF(e.target.result);
        document.getElementById('pdf-status-empty').style.display = 'none';
        document.getElementById('pdf-status-ready').style.display = 'block';
    };
    reader.readAsArrayBuffer(file);
}

function clearFile() {
    fileInput.value = '';
    document.getElementById('file-preview').style.display = 'none';
    document.getElementById('upload-text').innerHTML =
        '<strong style="color: var(--ds-violet-600);">Klik untuk pilih file</strong> atau drag & drop di sini';
    pdfPreview.destroy();
    document.getElementById('pdf-status-ready').style.display = 'none';
    document.getElementById('pdf-status-empty').style.display = 'block';
}

document.getElementById('signer-select').addEventListener('change', function() {
    const signerId = this.value;
    const signerName = this.options[this.selectedIndex]?.getAttribute('data-name');
    const signerEmail = this.options[this.selectedIndex]?.getAttribute('data-email');

    if (!signerId) {
        pdfPreview.setClickable(false);
        document.getElementById('pdf-instruction').style.display = 'none';
        currentSignerData = null;
    } else if (signers.find(s => s.id === signerId)) {
        alert('User ini sudah ditambahkan!');
        this.value = '';
        pdfPreview.setClickable(false);
        document.getElementById('pdf-instruction').style.display = 'none';
        currentSignerData = null;
    } else {
        currentSignerData = { id: signerId, name: signerName, email: signerEmail };
        pdfPreview.setClickable(true);
        pdfPreview.clearMarker();
        document.getElementById('pdf-instruction').style.display = 'block';
        document.getElementById('position-info').style.display = 'none';
        document.getElementById('add-signer-btn').style.display = 'none';
    }
});

function addSigner() {
    if (!currentSignerData || !pdfPreview.markerPosition) {
        alert('Pilih posisi pada PDF terlebih dahulu!');
        return;
    }

    const { id, name, email } = currentSignerData;
    const { page, x, y } = pdfPreview.markerPosition;

    signers.push({ id, name, email, page, x, y });
    renderSigners();

    document.getElementById('signer-select').value = '';
    currentSignerData = null;
    pdfPreview.setClickable(false);
    pdfPreview.clearMarker();
    document.getElementById('pdf-instruction').style.display = 'none';
    document.getElementById('position-info').style.display = 'none';
    document.getElementById('add-signer-btn').style.display = 'none';
}

function removeSigner(id) {
    signers = signers.filter(s => s.id !== id);
    renderSigners();
}

function renderSigners() {
    const list = document.getElementById('signers-list');
    const empty = document.getElementById('signers-empty');
    const hidden = document.getElementById('hidden-signers');

    empty.style.display = signers.length === 0 ? 'block' : 'none';
    hidden.innerHTML = '';

    if (signers.length === 0) { list.innerHTML = ''; return; }

    list.innerHTML = signers.map((s, i) => `
        <div style="display: flex; align-items: center; gap: .75rem; padding: .75rem 1rem;
             background: var(--ds-violet-50); border: 1px solid var(--ds-violet-100); border-radius: 10px; margin-bottom: .5rem;">
            <div style="width: 36px; height: 36px; background: var(--ds-violet-600); border-radius: 50%;
                 display: flex; align-items: center; justify-content: center;
                 color: #fff; font-size: .75rem; font-weight: 700; flex-shrink: 0;">
                ${s.name.substring(0, 2).toUpperCase()}
            </div>
            <div style="flex: 1;">
                <div style="font-size: .875rem; font-weight: 600; color: var(--ds-violet-800);">${s.name}</div>
                <div style="font-size: .72rem; color: var(--ds-violet-400);">${s.email}</div>
            </div>
            <div style="font-size: .72rem; color: var(--ds-violet-500); background: var(--ds-violet-100); padding: .25rem .6rem; border-radius: 6px; text-align: center;">
                Hal. ${s.page}<br>X:${Math.round(s.x)} Y:${Math.round(s.y)}
            </div>
            <button type="button" onclick="removeSigner('${s.id}')"
                    style="background: none; border: none; cursor: pointer; color: var(--ds-gray-400); font-size: 1.1rem;">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
    `).join('');

    signers.forEach((s, i) => {
        hidden.innerHTML += `
            <input type="hidden" name="signers[]" value="${s.id}">
            <input type="hidden" name="pages[]" value="${s.page}">
            <input type="hidden" name="x_positions[]" value="${Math.round(s.x)}">
            <input type="hidden" name="y_positions[]" value="${Math.round(s.y)}">
        `;
    });
}

document.getElementById('upload-form').addEventListener('submit', function(e) {
    if (signers.length === 0) {
        e.preventDefault();
        alert('Tambahkan minimal 1 signer sebelum mengirim!');
        return;
    }
    document.getElementById('submit-btn').innerHTML =
        '<i class="bi bi-hourglass-split"></i> Sedang memproses...';
    document.getElementById('submit-btn').disabled = true;
});
</script>
@endpush