<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{

    protected $fillable = [
        'backup_plan_id',
        'status',
        'backup_date',
        'start_time',
        'end_time',
         'final_size',
          'evidence',
          'remark',
          'created_by'
    ];

    public function plans()
{
    return $this->belongsTo(BackupPlan::class, 'backup_plan_id');
}

public function users()
{
    return $this->belongsTo(User::class, 'created_by');
}

}
