<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Department; // <- Tambahkan ini
use App\Models\User;       // <- Dan ini juga
use App\Models\Ticket;       // <- Dan ini juga

class Category extends Model
{
    protected $fillable = [
        'code',
        'department_id',
        'user_id',
        'description',
        'created_by',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
{
    return $this->hasMany(Ticket::class, 'category_id');
}

}
