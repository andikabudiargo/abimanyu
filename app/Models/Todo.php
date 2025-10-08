<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Todo extends Model
{
    protected $fillable = ['task', 'done', 'user_id', 'agenda_time']; // Tambahkan 'user_id' jika digunakan

   public function users()
{
    return $this->belongsToMany(User::class);
}


}
