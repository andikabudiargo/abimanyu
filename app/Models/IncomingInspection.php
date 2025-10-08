<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_number',
        'supplier_code',
        'article_code',
        'periode',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(IncomingInspectionItem::class, 'incoming_inspection_id');
    }

    
    public function user()
{
    return $this->belongsTo(User::class, 'created_by', 'id');
}

public function article()
{
    return $this->belongsTo(Article::class, 'article_code', 'article_code'); 
}

public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_code', 'code'); 
}
}
