<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    /**
     * Tampilkan daftar dokumen yang menunggu tanda tangan
     * dari user yang sedang login.
     */
    public function pending()
    {
        $pendingSigners = auth()->user()->documentSigners()
            ->where('status', 'pending')
            ->with('document.owner')
            ->latest()
            ->paginate(10);

        return view('documents.pending', compact('pendingSigners'));
    }

    /**
     * Tampilkan halaman untuk menandatangani dokumen.
     */
    public function show(Document $document)
    {
        $user = auth()->user();

        // Pastikan user ini memang signer yang masih pending untuk dokumen ini
        $signer = $document->signers()
            ->where('signer_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (! $signer) {
            abort(403, 'Anda tidak memiliki akses untuk menandatangani dokumen ini, atau Anda sudah menandatanganinya.');
        }

        $document->load('signers.signer');

        $position = $document->signaturePositions()
            ->where('signer_id', $user->id)
            ->first();

        return view('signatures.sign', compact('document', 'position'));
    }

    /**
     * Simpan tanda tangan (dari canvas atau upload gambar),
     * lalu cek apakah dokumen perlu dikunci otomatis.
     */
    public function store(Request $request, Document $document)
    {
        $user = auth()->user();

        $signer = $document->signers()
            ->where('signer_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->validate([
            'signature_type' => 'required|in:canvas,upload',
            'signature_data' => 'required_if:signature_type,canvas',
            'signature_file' => 'required_if:signature_type,upload|file|mimes:png,jpg,jpeg|max:2048',
        ]);

        // ===== Simpan gambar tanda tangan =====
        if ($request->signature_type === 'canvas') {
            // Data dari HTML5 Canvas berupa Base64 Data URI:
            // "data:image/png;base64,iVBORw0KG..."
            $imageData = $request->signature_data;
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = base64_decode($imageData);

            $filename = 'signatures/' . uniqid() . '_' . $user->id . '.png';
            Storage::disk('local')->put($filename, $imageData);
        } else {
            $filename = $request->file('signature_file')->store('signatures', 'local');
        }

        // ===== Simpan record signature =====
        Signature::create([
            'document_id'    => $document->id,
            'signer_id'      => $user->id,
            'signature_path' => $filename,
            'signed_at'      => now(),
        ]);

        // ===== Update status signer jadi "signed" =====
        $signer->update(['status' => 'signed']);

        AuditLog::record('SIGN_DOCUMENT', "Menandatangani dokumen: {$document->title}");

        // ===== Cek apakah SEMUA signer sudah menandatangani =====
        if ($document->allSigned()) {
            $document->update(['status' => 'locked']);
            AuditLog::record('DOCUMENT_LOCKED', "Dokumen terkunci otomatis: {$document->title}");
        }

        return redirect()->route('dashboard')
                         ->with('success', 'Tanda tangan berhasil disimpan!');
    }

    /**
     * Tampilkan gambar tanda tangan (untuk ditampilkan di halaman detail dokumen).
     */
    public function image($signatureId)
    {
        $signature = Signature::findOrFail($signatureId);

        // Otorisasi sederhana: hanya pemilik dokumen, signer terkait, atau admin
        $user     = auth()->user();
        $document = $signature->document;

        $allowed = $user->isAdmin()
            || $document->user_id === $user->id
            || $document->signers()->where('signer_id', $user->id)->exists();

        abort_unless($allowed, 403);

        return Storage::disk('local')->response($signature->signature_path);
    }
}
