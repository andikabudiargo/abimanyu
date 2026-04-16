<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCopy extends Model
{
    protected $fillable = ['registration_id', 'document_id', 'department_id', 'qty', 'evidence_path', 'socialization_date','socialized_by' ];

    public function document()
    {
        return $this->belongsTo(DocumentRegistration::class, 'registration_id');
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
