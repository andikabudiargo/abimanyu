<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'user_id',
        'note',
        'role'
    ];

    public function document()
    {
        return $this->belongsTo(DocumentRegistration::class, 'registration_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
