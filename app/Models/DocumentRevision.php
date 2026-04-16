<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'registration_id',
        'revision_number',
        'file_path',
        'file_4m_path',
        'before_change',
        'after_change',
    ];

    // Relasi ke document induk
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
 public function registration()
    {
        return $this->belongsTo(Document::class);
    }

}
