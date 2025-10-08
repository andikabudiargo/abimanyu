<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionDefect extends Model
{
    use HasFactory;

    protected $fillable = ['inspection_id', 'defect_id', 'qty', 'ok_repair', 'note'];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }
    // InspectionDefect.php
public function defect()
{
    return $this->belongsTo(Defect::class, 'defect_id');
}

}

