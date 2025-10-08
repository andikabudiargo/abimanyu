<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'article_code',
        'article_type',
        'warehouse_id',
        'qty',
        'status',
    ];

    public $timestamps = true; // Aktifkan jika kamu menggunakan created_at & updated_at

     public function article()
{
    return $this->belongsTo(Article::class, 'article_code', 'article_code');
}

public function location()
{
    return $this->belongsTo(Warehouse::class, 'warehouse_id');
}
}

