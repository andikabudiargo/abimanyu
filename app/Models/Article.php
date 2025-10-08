<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    protected $fillable = [
        'article_code',
        'supplier_code',
        'article_type',
        'description',
        'supplier',
        'unit',
        'color',
        'model',
        'safety_stock',
        'min_package',
        'qr_code_path', // <--- INI WAJIB ADA
    ];

    public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_code', 'code');
}

public function ArticleType()
{
    return $this->belongsTo(ArticleType::class, 'article_type');
}

public function type()
{
    return $this->belongsTo(ArticleType::class, 'article_type', 'code');
}


    public $timestamps = true; // Aktifkan jika kamu menggunakan created_at & updated_at
}
