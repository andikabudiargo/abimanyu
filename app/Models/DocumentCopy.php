<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCopy extends Model
{
    protected $fillable = [
    'registration_id', 'document_id', 'department_id', 'qty', 'size',
    'evidence_path', 'socialization_date', 'socialized_by',
    'copies_taken', 'copies_taken_from', 'copies_taken_at', 'taken_evidence',
];

protected $casts = [
    'copies_taken'    => 'boolean',
    'copies_taken_at' => 'datetime',
];

public function takenFrom()
{
    return $this->belongsTo(User::class, 'copies_taken_from');
}

    public function registration()
    {
        return $this->belongsTo(DocumentRegistration::class, 'registration_id');
    }

    public function department()
{
    return $this->belongsTo(Department::class, 'department_id');
}

 public function receivedBy()
{
    return $this->belongsTo(User::class, 'received_by');
}

public function socialized()
{
    return $this->belongsTo(User::class, 'socialized_by');
}
}
