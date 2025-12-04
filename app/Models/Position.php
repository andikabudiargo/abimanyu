<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $table = 'job_positions';
    protected $fillable = [
        'name', 
        'description', 
        'level_id', 
        'grade_id', 
        'status'
    ];

   // Relasi many-to-many ke Employee
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'position_departments', 'position_id', 'dept_id');
    }

    // Relasi ke career path / level
    public function level(): BelongsTo
    {
        return $this->belongsTo(CareerPath::class, 'level_id', 'id');
    }

    // Relasi ke salary grade
    public function grade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'grade_id', 'id');
    }

    public function employees()
{
    return $this->belongsToMany(
        Employee::class,
        'employee_positions', // pivot table
        'position_id',        // FK ke position
        'employee_id'         // FK ke employee
    );
}


    public function children(): HasMany
{
    return $this->hasMany(Position::class, 'report_to', 'id')
                ->with('employee'); // Hanya employee, jangan recursive children
}


    // Relasi ke parent position
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'report_to', 'id');
    }
}
