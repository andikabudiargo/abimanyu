<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAPAAuditor extends Model
{
    use HasFactory;

    protected $table = 'capa_auditors';
    protected $fillable = [
        'capa_id','user_id'
    ];

     public function users()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
public function capa()
{
    return $this->belongsTo(CAPA::class, 'capa_id');
}
}
