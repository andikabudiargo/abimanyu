<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

    protected $fillable = [
        'code', 'name', 'initial', 'category', 'as_customer',
        'join_date', 'coa_hutang', 'coa_retur', 'address',
        'provinsi', 'city', 'kecamatan', 'kelurahan', 'postal_code',
        'contact_person', 'telephone', 'mobile_phone', 'fax', 'email',
        'top', 'pkp', 'npwp_number', 'npwp_name', 'npwp_address',
        'bank_type', 'bank_name', 'branch', 'account_bank_name', 'account_bank_number'
    ];

    // App\Models\Supplier.php
public function purchaseRequests()
{
    return $this->hasMany(PurchaseRequest::class);
}

}

