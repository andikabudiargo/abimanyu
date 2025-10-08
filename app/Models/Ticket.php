<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TicketHold;
use App\Models\Category;

class Ticket extends Model
{
    protected $fillable = [
    'ticket_number', 'category_id', 'title', 'description', 'attachment',
    'status', 'priority', 'request_by', 'approved_by',
    'approved_at', 'rejected_reason', 'created_at'
];

// === SCOPE UNTUK ORDER STATUS ===
   public function scopeOrderByStatus($query)
{
    return $query->orderByRaw("FIELD(tickets.status, 'Pending', 'Approved', 'Work in Progress', 'On Hold', 'Done', 'Closed', 'Rejected')")
                 ->orderBy('tickets.created_at', 'desc');
}


public function requestor()
{
    return $this->belongsTo(User::class, 'request_by');
}

public function approved()
{
    return $this->belongsTo(User::class, 'approved_by');
}

public function reject()
{
    return $this->belongsTo(User::class, 'reject_by');
}

public function process()
{
    return $this->belongsTo(User::class, 'processed_by');
}

public function holds()
{
    return $this->hasMany(TicketHold::class);
}

public function category()
{
    return $this->belongsTo(Category::class, 'category_id'); // 👈 tambahkan 'category' sebagai foreign key
}

public function attachments()
{
    return $this->hasMany(TicketAttachment::class, 'ticket_id');
}

public function evidences()
{
    return $this->hasMany(TicketEvidence::class,'ticket_id');
}





}
