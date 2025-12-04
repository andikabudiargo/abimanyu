<?php

namespace App\Models;

use App\Models\APDReturnItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APDReturn extends Model
{
    use HasFactory;
    protected $table = 'apd_returns';

    protected $fillable = [
        'return_number',
        'return_date',
        'note',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany(APDReturnItem::class);
    }
}
?>