<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atk extends Model
{
    protected $fillable = [
        'name',
        'initial_stock',
        'min_stock',
        'uom',
        'photo',
        'created_by'
    ];

     public function adjustments()
    {
        return $this->belongsTo(AtkAdjustment::class);
    }

    public function departemen(){
        return $this->belongsTo(Department::class, 'department', 'id');
    }

    // Accessor untuk URL foto langsung dari model
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? \Storage::url($this->photo) : null;
    }
}