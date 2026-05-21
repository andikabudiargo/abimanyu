<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoItem extends Model
{
    use HasFactory;

    protected $table = 'sto_items';

    protected $fillable = [
        'sto_id',
        'article_code',
        'qty',
        'qty_2',
        'kondisi',
        'location',
        'other_name',
        'uom'
    ];

    /* =====================
     | RELATIONSHIPS
     ===================== */

    public function sto()
    {
        return $this->belongsTo(Sto::class, 'sto_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_code');
    }
}
