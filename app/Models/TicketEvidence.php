<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketEvidence extends Model
{
    use HasFactory;

    protected $table = 'ticket_evidences'; // Nama tabel

    protected $fillable = [
        'ticket_id',
        'path',
    ];

    /**
     * Relasi ke Ticket
     * Satu evidence hanya untuk satu ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
