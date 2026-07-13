@extends('layouts.landing')

@section('content')

{{-- ===== NAVBAR ===== --}}
<nav class="lp-nav">
    <div class="lp-nav-inner">
        <a href="{{ route('landing') }}" class="lp-brand">
            <span class="brand-icon"><i class="bi bi-pen-fill"></i></span>
            <span>DigiSign</span>
        </a>
        <div class="lp-nav-links">
            <a href="#fitur">Fitur</a>
            <a href="#cara-kerja">Cara Kerja</a>
            <a href="#keamanan">Keamanan</a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('login') }}" class="btn-ds-outline" style="padding: .5rem 1.1rem;">Masuk</a>
            <a href="{{ route('register') }}" class="btn-ds-primary">
                Mulai Gratis <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</nav>

{{-- ===== HERO ===== --}}
<section class="lp-hero">
    <div>
        <span class="lp-eyebrow">
            <i class="bi bi-shield-check"></i> Diverifikasi dengan hash SHA-256
        </span>
        <h1>Tanda tangan dokumen, <em>selesai dalam hitungan menit.</em></h1>
        <p class="lead">
            DigiSign membantu tim Anda mengunggah dokumen, menentukan penandatangan,
            dan menyelesaikan proses tanda tangan secara digital — tanpa cetak, tanpa
            kurir, dan setiap dokumen dapat diverifikasi keasliannya kapan saja.
        </p>
        <div class="lp-cta-row">
            <a href="{{ route('register') }}" class="btn-ds-primary" style="padding: .75rem 1.6rem; font-size: .95rem;">
                Mulai Sekarang <i class="bi bi-arrow-right"></i>
            </a>
            <a href="#cara-kerja" class="btn-ds-outline" style="padding: .75rem 1.6rem; font-size: .95rem;">
                Lihat Cara Kerja
            </a>
        </div>

        <div class="lp-trust">
            <div>
                256-bit
                <span>Enkripsi hash dokumen</span>
            </div>
            <div>
                100%
                <span>Bebas kertas</span>
            </div>
            <div>
                Tanpa batas
                <span>Penandatangan per dokumen</span>
            </div>
        </div>
    </div>

    {{-- Hero visual: floating document cards --}}
    <div class="lp-hero-visual">
        <div class="lp-doc-card main">
            <div class="doc-card-head">
                <div class="ic"><i class="bi bi-file-earmark-text"></i></div>
                <div class="meta">
                    Perjanjian Kerja Sama.pdf
                    <small>Diunggah oleh Admin · hari ini</small>
                </div>
            </div>
            <div class="lines">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="sig-row">
                <span><span class="status-dot"></span>2 dari 2 sudah tanda tangan</span>
                <span style="color: var(--ds-violet-600); font-weight: 700;">Selesai</span>
            </div>
        </div>

        <div class="lp-doc-card floating">
            <div class="doc-card-head">
                <div class="ic"><i class="bi bi-fingerprint"></i></div>
                <div class="meta">
                    Integritas Terverifikasi
                    <small>SHA-256 cocok</small>
                </div>
            </div>
            <div class="hash-box" style="font-size: .62rem;">a3f2c9e1...8d4b</div>
        </div>
    </div>
</section>

{{-- ===== FITUR ===== --}}
<section class="lp-section" id="fitur">
    <div class="lp-section-head">
        <span class="lp-kicker">Fitur Utama</span>
        <h2>Semua yang dibutuhkan untuk menyelesaikan dokumen.</h2>
        <p>
            Dari unggah dokumen hingga verifikasi akhir, DigiSign menyatukan seluruh
            proses tanda tangan dalam satu alur kerja yang sederhana.
        </p>
    </div>

    <div class="lp-features">
        <div class="lp-feature-card">
            <div class="fc-icon"><i class="bi bi-cloud-upload"></i></div>
            <h3>Unggah Dokumen PDF</h3>
            <p>Unggah dokumen dan tentukan judul serta deskripsi singkat. Sistem menghitung hash dokumen secara otomatis saat diunggah.</p>
        </div>
        <div class="lp-feature-card">
            <div class="fc-icon"><i class="bi bi-people"></i></div>
            <h3>Multi Penandatangan</h3>
            <p>Tambahkan lebih dari satu penandatangan untuk satu dokumen, masing-masing dengan posisi tanda tangan tersendiri.</p>
        </div>
        <div class="lp-feature-card">
            <div class="fc-icon"><i class="bi bi-pen"></i></div>
            <h3>Tanda Tangan Digital</h3>
            <p>Gambar tanda tangan langsung lewat kanvas, atau unggah gambar tanda tangan yang sudah ada.</p>
        </div>
        <div class="lp-feature-card">
            <div class="fc-icon"><i class="bi bi-lock"></i></div>
            <h3>Kunci Otomatis</h3>
            <p>Setelah seluruh penandatangan menyelesaikan tanda tangan, dokumen otomatis terkunci dan tidak dapat diubah lagi.</p>
        </div>
        <div class="lp-feature-card">
            <div class="fc-icon"><i class="bi bi-shield-check"></i></div>
            <h3>Verifikasi Keaslian</h3>
            <p>Unggah kembali dokumen kapan saja untuk memastikan isinya belum berubah sejak ditandatangani.</p>
        </div>
        <div class="lp-feature-card">
            <div class="fc-icon"><i class="bi bi-journal-text"></i></div>
            <h3>Jejak Audit Lengkap</h3>
            <p>Setiap aktivitas — unggah, tambah penandatangan, tanda tangan, verifikasi — tercatat dengan waktu dan pelaku yang jelas.</p>
        </div>
    </div>
</section>

{{-- ===== CARA KERJA ===== --}}
<section class="lp-section" id="cara-kerja" style="background: var(--ds-violet-50); border-radius: 32px;">
    <div class="lp-section-head">
        <span class="lp-kicker">Cara Kerja</span>
        <h2>Empat langkah menuju dokumen yang sah.</h2>
        <p>Tidak perlu pelatihan khusus. Siapapun di tim Anda dapat menyelesaikan proses ini sendiri.</p>
    </div>

    <div class="lp-steps">
        <div class="lp-step">
            <span class="step-num">01</span>
            <h4>Unggah Dokumen</h4>
            <p>Pilih file PDF, beri judul, dan sistem akan menghitung hash SHA-256 secara otomatis.</p>
        </div>
        <div class="lp-step">
            <span class="step-num">02</span>
            <h4>Tentukan Penandatangan</h4>
            <p>Tambahkan satu atau lebih penandatangan beserta posisi tanda tangan pada dokumen.</p>
        </div>
        <div class="lp-step">
            <span class="step-num">03</span>
            <h4>Tanda Tangan</h4>
            <p>Setiap penandatangan menerima undangan dan menyelesaikan tanda tangan secara digital.</p>
        </div>
        <div class="lp-step">
            <span class="step-num">04</span>
            <h4>Dokumen Terkunci</h4>
            <p>Setelah semua pihak menandatangani, dokumen otomatis menjadi <em>read-only</em> dan dapat diverifikasi.</p>
        </div>
    </div>
</section>

{{-- ===== KEAMANAN ===== --}}
<section class="lp-section" id="keamanan">
    <div class="lp-security">
        <div>
            <h2>Keamanan bukan fitur tambahan — itu fondasinya.</h2>
            <p>
                DigiSign dibangun dengan praktik keamanan standar industri: enkripsi
                password, kontrol akses berbasis peran, dan verifikasi integritas
                dokumen pada setiap tahap proses.
            </p>
        </div>
        <div class="lp-security-grid">
            <div class="lp-security-item">
                <i class="bi bi-key ic"></i>
                <h4>Enkripsi Password</h4>
                <p>Seluruh kata sandi pengguna disimpan menggunakan algoritma bcrypt.</p>
            </div>
            <div class="lp-security-item">
                <i class="bi bi-fingerprint ic"></i>
                <h4>Hash SHA-256</h4>
                <p>Setiap dokumen memiliki sidik jari digital unik untuk deteksi perubahan.</p>
            </div>
            <div class="lp-security-item">
                <i class="bi bi-person-badge ic"></i>
                <h4>Kontrol Akses Peran</h4>
                <p>Admin dan pengguna memiliki batasan akses yang jelas dan terpisah.</p>
            </div>
            <div class="lp-security-item">
                <i class="bi bi-journal-check ic"></i>
                <h4>Audit Trail</h4>
                <p>Riwayat aktivitas tersimpan permanen dan tidak dapat dimanipulasi.</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== CTA BANNER ===== --}}
<section class="lp-section">
    <div class="lp-cta-banner">
        <h2>Siap menyelesaikan dokumen tanpa kertas?</h2>
        <p>Buat akun dan unggah dokumen pertama Anda hari ini.</p>
        <a href="{{ route('register') }}" class="btn-ds-primary" style="padding: .8rem 1.85rem; font-size: .95rem;">
            Buat Akun Gratis <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</section>

{{-- ===== FOOTER ===== --}}
<footer class="lp-footer">
    <div class="lp-footer-inner">
        <span>© {{ date('Y') }} DigiSign. Proof of Concept untuk keperluan akademik.</span>
        <a href="{{ route('login') }}" style="color: var(--ds-gray-600); text-decoration: none; font-weight: 500;">Masuk ke akun</a>
    </div>
</footer>

@endsection
