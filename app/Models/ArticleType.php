<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleType extends Model
{
     use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'warehouse_id',
        'note',
        'created_by',
    ];

    public function warehouse()
{
    return $this->belongsTo(Warehouse::class);
}

}
