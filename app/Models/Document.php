<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['document_number', 'document_type', 'title', 'reason', 'current_version'];

     public $timestamps = false; // 🚀 ini wajib kalau tidak ada created_at / updated_at

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

     public function authorized()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function notes()
{
    return $this->hasMany(DocumentNote::class);
}

 // Relasi ke revisions
    public function revisions()
    {
        return $this->hasMany(DocumentRevision::class);
    }

      // Ambil revisi terakhir
    public function latestRevision()
    {
        return $this->hasOne(DocumentRevision::class)->latestOfMany();
    }

}
