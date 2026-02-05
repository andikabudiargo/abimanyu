<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAPAEvidence extends Model
{
    use HasFactory;

    protected $table = 'capa_evidences';
    protected $fillable = [
        'capa_id','file_name'
    ];

   public function capa()
{
    return $this->belongsTo(CAPA::class);
}

}
