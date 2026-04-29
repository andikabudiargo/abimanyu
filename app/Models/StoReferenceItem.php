<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoReferenceItem extends Model
{
    protected $table = 'sto_reference_items';

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_code', 'article_code');
        // sesuaikan nama model dan kolom FK/PK article milik kamu
    }
}