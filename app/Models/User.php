<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Department;
use App\Models\Role;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

      protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'status',
        'avatar',         // <- Tambahkan ini jika menyimpan avatar file
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'password' => 'hashed', // Laravel >=10
    ];

    public function departments()
{
    return $this->belongsToMany(Department::class, 'department_user');
}

public function roles()
{
    return $this->belongsToMany(Role::class, 'role_user');
}

// di User.php
public function hasRole($name)
{
    return $this->roles->contains('name', $name);
}

public function hasDepartment($name)
{
    return $this->departments->contains('name', $name);
}
public function hasTechnician($departmentId)
{
    return $this->department && $this->department->id === $departmentId;
}


public function todos()
{
    return $this->belongsToMany(Todo::class);
}

    // Scope aktif, bisa digunakan untuk memfilter user aktif
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
