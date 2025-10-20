<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueTrackerMaterial extends Model
{
    protected $fillable = [
        'issue_tracker_id', 'material', 'qty', 'uom', 'vendor', 'price', 'subtotal'
    ];

    public function issue()
    {
        return $this->belongsTo(IssueTracker::class, 'issue_tracker_id');
    }
}
?>