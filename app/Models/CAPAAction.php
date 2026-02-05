<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAPAAction extends Model
{
    use HasFactory;

    protected $table = 'capa_actions';
    protected $fillable = [
        'capa_id','type','description','pic','due_date'
    ];

   public function capa()
{
    return $this->belongsTo(CAPA::class);
}

public function picUser()
    {
        return $this->belongsTo(User::class, 'pic', 'id');
    }

}
