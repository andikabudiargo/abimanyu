<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCopy extends Model
{
    protected $fillable = ['document_id', 'department_id', 'qty', 'document_revision_id'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function department()
{
    return $this->belongsTo(Department::class, 'department_id');
}

 public function socialized()
    {
        return $this->belongsTo(User::class, 'socialized_by');
    }
}
