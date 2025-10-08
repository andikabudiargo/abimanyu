<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BackupPlan extends Model
{

    protected $fillable = [
        'job_name',
        'backup_type',
        'source',
        'target',
        'frequency',
         'retention_policy',
          'pic',
    ];

     public function sources()
{
    return $this->belongsTo(Storage::class, 'source');
}

 public function targets()
{
    return $this->belongsTo(Storage::class, 'target');
}

 public function users()
{
    return $this->belongsTo(User::class, 'pic');
}

}
