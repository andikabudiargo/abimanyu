<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHold extends Model
{

      protected $fillable = [
        'ticket_id',
        'reason',
        'description',
        'start_at',
        'end_at',
        'created_by',
    ];
    public function ticket()
{
    return $this->belongsTo(Ticket::class);
}

public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}


}
