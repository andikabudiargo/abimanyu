<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingInspectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_inspection_id',
        'inspection_id',
    ];

    public function incomingInspection()
    {
        return $this->belongsTo(IncomingInspection::class, 'incoming_inspection_id');
    }

    public function inspection()
{
    return $this->belongsTo(Inspection::class);
}
}
