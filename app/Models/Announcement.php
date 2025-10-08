<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'display_start',
        'display_end',
        'created_by'
    ];

    public function attachments()
    {
        return $this->hasMany(AnnouncementAttachment::class);
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'announcement_recipient');
    }

    public function createdBy() {
    return $this->belongsTo(User::class, 'created_by');
}
}
