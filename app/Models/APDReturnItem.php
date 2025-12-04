<?php

namespace App\Models;

use App\Models\Apd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APDReturnItem extends Model
{
    use HasFactory;
    protected $table = 'apd_return_items';

    protected $fillable = [
        'apd_return_id',
        'returned_from',
        'apd_id',
        'qty',
        'conditions',
    ];

    public function return()
    {
        return $this->belongsTo(APDReturn::class);
    }

    public function apd()
    {
        return $this->belongsTo(Apd::class, 'apd_id');
    }
}
?>