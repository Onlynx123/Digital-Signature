<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Document;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    /**
     * Tampilkan halaman verifikasi (kosong, belum ada hasil).
     */
    public function index()
    {
        return view('verify.index');
    }

    /**
     * Proses verifikasi: hitung hash file yang diupload,
     * lalu cocokkan dengan database.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480',
        ]);

        $uploadedFile = $request->file('file');
        $uploadedHash = hash('sha256', file_get_contents($uploadedFile->getRealPath()));

        // Cari dokumen dengan hash yang sama persis
        $document = Document::where('hash_value', $uploadedHash)->first();

        $result = [
            'hash'     => $uploadedHash,
            'valid'    => $document !== null,
            'document' => $document,
        ];

        AuditLog::record(
            'VERIFY_DOCUMENT',
            $document
                ? "Verifikasi VALID untuk dokumen: {$document->title}"
                : "Verifikasi GAGAL: hash tidak ditemukan di database ({$uploadedHash})"
        );

        return view('verify.index', compact('result'));
    }
}
