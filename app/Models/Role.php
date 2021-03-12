<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;
use App\User;

class Role extends Model
{
    protected $fillable = [
        'name', 'slug', 'alias'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles');
    }

    public function complaints()
    {
        return $this->hasMany(\App\Models\Complaint::class, 'type_id', 'id');
    }
}
