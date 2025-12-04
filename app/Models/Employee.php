<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        // Personal Info
        'name',
        'gender',
        'birth_date',
        'birth_place',
        'religion',
        'nationality',

        // Family / PTKP
        'marital_status',
        'dependents',
        'spouse_works',

        // Education & Job
        'last_education',
        'employee_type',
        'instansi',
        'nik',
        'join_date',
        'position_id',
        'department',
        'section',

        // Contact
        'address',
        'provinsi',
        'city',
        'kecamatan',
        'kelurahan',
        'phone',
        'phone_emergency_1',
        'phone_emergency_2',
        'email',

        // Official Numbers
        'ktp_number',
        'kk_number',
        'npwp_number',
        'bpjs_ketenagakerjaan',
        'bpjs_kesehatan',
        'bank_number',
        'bank_account_name',

        // Documents / Files
        'photo_profile',
        'cv',
        'ktp_file',
        'kk_file',
        'ijazah',
        'skck',
        'mcu',
    ];

     public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_employee', 'employee_id', 'dept_id');
    }
public function positions()
{
    return $this->belongsToMany(
        Position::class,
        'employee_positions', // pivot table
        'employee_id',        // FK ke employee
        'position_id'         // FK ke position
    );
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
