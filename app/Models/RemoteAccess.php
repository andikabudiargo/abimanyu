<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RemoteAccess extends Model
{

    protected $table = 'remote_access';

    protected $fillable = [
        'asset_id',
        'software',
        'remote_id',
        'password',
    ];

    public function employee()
{
    return $this->belongsTo(Employee::class, 'asset_id');
}


}

