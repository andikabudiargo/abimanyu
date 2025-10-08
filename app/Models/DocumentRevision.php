<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'version',
        'reason_revision',
        'file',
        'file_4m',
        'remark',
        'created_by',
        'review_by',
        'review_at'
    ];

    // Relasi ke document induk
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
 public function copies()
    {
        return $this->hasMany(DocumentCopy::class);
    }

    public function requestor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reject()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

     public function approval()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

      public function review()
    {
        return $this->belongsTo(User::class, 'review_by');
    }

     public function authorized()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function notes()
{
    return $this->hasMany(DocumentNote::class);
}
}
