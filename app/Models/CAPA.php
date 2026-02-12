<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAPA extends Model
{
    use HasFactory;

    protected $table = 'capas';
    protected $fillable = [
        'audit_id','capa_number','report_date','source_of_finding','category',
        'dept_id','dept_representative','detail_of_information','problem',
        'status','created_by', 'posted_by', 'posted_at', 'verified_by', 'verified_at', 'processed_by', 'processed_at', 'review_by', 'review_at',
        'submitted_by','submitted_at','returned_by','returned_at','authorized_by','authorized_at', 'approved_by', 'approved_at','new_capa_needed',
        'new_capa_reason',
        'mr_statement',
    ];

     public function user()
{
    return $this->belongsTo(User::class, 'created_by', 'id');
}

public function auditors()
{
    return $this->hasMany(CAPAAuditor::class, 'capa_id', 'id')->with('users');
}


    public function departemen(){
        return $this->belongsTo(Department::class, 'dept_id', 'id');
    }

    public function representative(){
        return $this->belongsTo(User::class,'dept_representative', 'id');
    }

      public function comments(){
        return $this->hasMany(CAPACommentary::class,'capa_id', 'id');
    }

    public function actions()
{
    return $this->hasMany(CAPAAction::class, 'capa_id', 'id');
}


public function rca()
{
    return $this->hasOne(CAPAAction::class, 'capa_id', 'id')
                ->where('type', 'RCA');
}


public function ca()
{
    return $this->hasOne(CAPAAction::class, 'capa_id', 'id')
                ->where('type', 'CA');
}


public function pa()
{
    return $this->hasOne(CAPAAction::class, 'capa_id', 'id')
                ->where('type', 'PA');
}

public function evidences()
{
    return $this->hasMany(CAPAEvidence::class, 'capa_id');
}

public function createdBy(){
        return $this->belongsTo(User::class,'created_by', 'id');
    }

    public function postedBy(){
        return $this->belongsTo(User::class,'posted_by', 'id');
    }

     public function verifiedBy(){
        return $this->belongsTo(User::class,'verified_by', 'id');
    }

     public function returnedBy(){
        return $this->belongsTo(User::class,'returned_by', 'id');
    }

public function authorizedBy(){
        return $this->belongsTo(User::class,'authorized_by', 'id');
    }

    public function submittedBy(){
        return $this->belongsTo(User::class,'submitted_by', 'id');
    }

    public function approvedBy(){
        return $this->belongsTo(User::class,'approved_by', 'id');
    }

    public function processedBy(){
        return $this->belongsTo(User::class,'processed_by', 'id');
    }

    public function reviewBy(){
        return $this->belongsTo(User::class,'review_by', 'id');
    }

    public function getDepartmentDisplayAttribute()
{
    if (in_array($this->dept_id, [2,3,5])) {
        return 'HRGAIT';
    }

    return $this->departemen->name ?? 'No Department';
}


}
