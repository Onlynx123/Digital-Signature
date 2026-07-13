<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Tampilkan daftar dokumen.
     * Admin lihat semua dokumen, user biasa hanya lihat dokumen miliknya
     * (sebagai pemilik) -- bisa diperluas untuk dokumen yang ia jadi signer juga.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = $user->isAdmin()
            ? Document::with('owner', 'signers')->latest()
            : $user->documents()->with('signers')->latest();

        // Filter pencarian judul
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $documents = $query->paginate(15);

        return view('documents.index', compact('documents'));
    }

    /**
     * Tampilkan form upload dokumen baru.
     */
    public function create()
    {
        // Daftar user lain yang bisa dipilih sebagai signer
        $users = User::where('id', '!=', auth()->id())
                     ->where('is_active', true)
                     ->orderBy('name')
                     ->get(['id', 'name', 'email']);

        return view('documents.create', compact('users'));
    }

    /**
     * Simpan dokumen baru: upload file, generate hash, tambah signer & posisi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'file'          => 'required|file|mimes:pdf|max:20480', // 20 MB
            'signers'       => 'required|array|min:1',
            'signers.*'     => 'exists:users,id',
            'pages'         => 'required|array',
            'pages.*'       => 'integer|min:1',
            'x_positions'   => 'required|array',
            'x_positions.*' => 'numeric|min:0',
            'y_positions'   => 'required|array',
            'y_positions.*' => 'numeric|min:0',
        ]);

        // 1. Simpan file PDF ke storage (disk lokal, BUKAN public, agar aman)
        $file     = $request->file('file');
        $filePath = $file->store('documents', 'local');

        // 2. Generate hash SHA-256 dari isi file untuk verifikasi integritas
        $hashValue = hash('sha256', file_get_contents($file->getRealPath()));

        // 3. Buat record dokumen
        $document = Document::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $filePath,
            'status'      => 'waiting_signature',
            'hash_value'  => $hashValue,
        ]);

        // 4. Tambahkan setiap signer beserta posisi tanda tangannya
        foreach ($request->signers as $i => $signerId) {
            $document->signers()->create([
                'signer_id' => $signerId,
                'status'    => 'pending',
            ]);

            $document->signaturePositions()->create([
                'signer_id'   => $signerId,
                'page_number' => $request->pages[$i] ?? 1,
                'x_position'  => $request->x_positions[$i] ?? 100,
                'y_position'  => $request->y_positions[$i] ?? 300,
            ]);
        }

        // 5. Catat ke audit log
        AuditLog::record('UPLOAD_DOCUMENT', "Mengunggah dokumen: {$document->title}");

        return redirect()->route('documents.show', $document)
                         ->with('success', 'Dokumen berhasil diunggah dan undangan tanda tangan telah dikirim.');
    }

    /**
     * Tampilkan detail dokumen + status semua signer.
     */
    public function show(Document $document)
    {
        $this->authorizeDocument($document);

        $document->load([
            'owner',
            'signers.signer',
            'signaturePositions.signer',
            'signatures.signer',
        ]);

        AuditLog::record('VIEW_DOCUMENT', "Melihat dokumen: {$document->title}");

        return view('documents.show', compact('document'));
    }


    /**
 * Preview PDF di browser.
 */
public function preview(Document $document)
{
    $this->authorizeDocument($document);

    $path = storage_path('app/private/' . $document->file_path);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline',
    ]);
}

    /**
     * Download file PDF asli.
     */
    public function download(Document $document)
    {
        $this->authorizeDocument($document);

        return Storage::disk('local')->download(
            $document->file_path,
            $document->title . '.pdf'
        );
    }

    /**
     * Hapus dokumen (hanya jika belum terkunci).
     */
    public function destroy(Document $document)
    {
        $this->authorizeDocument($document);

        if ($document->isLocked()) {
            return back()->with('error', 'Dokumen yang sudah terkunci tidak dapat dihapus.');
        }

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        AuditLog::record('DELETE_DOCUMENT', "Menghapus dokumen: {$document->title}");

        return redirect()->route('documents.index')
                         ->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Helper otorisasi: pastikan user boleh mengakses dokumen ini.
     * Boleh akses jika: admin, pemilik dokumen, atau salah satu signer.
     */
    private function authorizeDocument(Document $document): void
    {
        $user = auth()->user();

        if ($user->isAdmin() || $document->user_id === $user->id) {
            return;
        }

        $isSigner = $document->signers()
            ->where('signer_id', $user->id)
            ->exists();

        if (! $isSigner) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }
    }
}
