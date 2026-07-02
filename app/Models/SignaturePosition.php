<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignaturePosition extends Model
{
    protected $fillable = [
        'document_id',
        'signer_id',
        'page_number',
        'x_position',
        'y_position',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_id');
    }
}
