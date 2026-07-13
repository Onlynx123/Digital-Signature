<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'status',
        'hash_value',
    ];

    // ============================================
    // RELASI
    // ============================================

    /** Pemilik / pengunggah dokumen */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Daftar signer (penandatangan) untuk dokumen ini */
    public function signers()
    {
        return $this->hasMany(DocumentSigner::class);
    }

    /** Posisi tanda tangan per signer */
    public function signaturePositions()
    {
        return $this->hasMany(SignaturePosition::class);
    }

    /** Tanda tangan yang sudah disimpan untuk dokumen ini */
    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /** Cek apakah SEMUA signer sudah menandatangani */
    public function allSigned(): bool
    {
        return $this->signers()->where('status', 'pending')->count() === 0;
    }

    /** Cek apakah dokumen sudah terkunci (read-only) */
    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }
}
