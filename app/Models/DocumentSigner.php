<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSigner extends Model
{
    protected $fillable = [
        'document_id',
        'signer_id',
        'status',
    ];

    /** Dokumen yang terkait */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /** User yang menjadi signer */
    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_id');
    }
}
