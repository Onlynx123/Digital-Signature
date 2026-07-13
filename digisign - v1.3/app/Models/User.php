<?php

namespace App\Models;

// Note: use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Kolom yang boleh diisi secara massal (mass assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * Kolom yang disembunyikan saat model di-serialize (misal ke JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data otomatis
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed', // otomatis bcrypt saat disimpan
            'is_active'         => 'boolean',
        ];
    }

    // ============================================
    // RELASI (Eloquent Relationships)
    // ============================================

    /** Dokumen yang diunggah oleh user ini */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /** Daftar dokumen di mana user ini menjadi signer */
    public function documentSigners()
    {
        return $this->hasMany(DocumentSigner::class, 'signer_id');
    }

    /** Tanda tangan yang pernah dibuat user ini */
    public function signatures()
    {
        return $this->hasMany(Signature::class, 'signer_id');
    }

    /** Log aktivitas milik user ini */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /** Cek apakah user adalah admin */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Ambil daftar signer record yang masih pending untuk user ini */
    public function pendingSignatures()
    {
        return $this->documentSigners()->where('status', 'pending');
    }
}
