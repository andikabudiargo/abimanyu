<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'shift', 'inspection_date', 'part_name', 'supplier_code', 'qty_received',
        'inspection_post', 'check_method', 'note', 'spraybooth',
        'total_check', 'total_ok', 'total_ok_repair', 'total_ng',
        'inspection_number' // ✅ tambahkan ini
    ];

    public function defects()
    {
        return $this->hasMany(InspectionDefect::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}

public function article()
{
    return $this->belongsTo(Article::class, 'part_name', 'article_code'); 
}

public function supplier()
{
    return $this->belongsTo(Supplier::class, 'supplier_code', 'code');
}

public function getPartnerAttribute()
{
    if ($this->inspection_post === 'Incoming') {
        return $this->supplier;
    }

    return Customer::where('code', $this->supplier_code)->first();
}


public function inspection_defects()
{
    return $this->hasMany(InspectionDefect::class, 'inspection_id');
}



}

