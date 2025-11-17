<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'nik',
        'name',
    ];

     public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_employee', 'employee_id', 'dept_id');
    }

    public function distributions()
{
    return $this->hasMany(APDDistributionItem::class, 'receiver', 'id')
        ->with(['distribution', 'apd', 'distribution.creator']);
}

public function remoteAccess()
{
    return $this->hasOne(RemoteAccess::class, 'asset_id');
}



}
