<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roles extends Model
{
    use HasFactory;

    protected $fillable = ['id','name','all','module','group','permissions','sequence','status'];
    protected $table = "user_roles";
    protected $primaryKey = "id";
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\RolesFactory::new();
    }

    public function permissions()
    {
        return $this->hasManyThrough(
            'Modules\User\Entities\Permissions',
            'Modules\User\Entities\RolesAndPermissions',
            'role_id', // Foreign key on the RolesAdminPermissions table...
            'id', // Foreign key on the permission table...
            'id', // Local key on the role table...
            'permission_id' // Local key on the RolesAdminPermissions table...
        );
    }

    public function permissions_id()
    {
        return $this->hasManyThrough(
            'Modules\User\Entities\Permissions',
            'Modules\User\Entities\RolesAndPermissions',
            'role_id', // Foreign key on the RolesAdminPermissions table...
            'id', // Foreign key on the permission table...
            'id', // Local key on the role table...
            'permission_id' // Local key on the RolesAdminPermissions table...
        )->select('user_permissions.id','name');
    }

    public function permissions_group()
    {
        return $this->hasManyThrough(
            'Modules\User\Entities\Permissions',
            'Modules\User\Entities\RolesAndPermissions',
            'role_id', // Foreign key on the RolesAdminPermissions table...
            'id', // Foreign key on the permission table...
            'id', // Local key on the role table...
            'permission_id' // Local key on the RolesAdminPermissions table...
        )->select('user_permissions.id','module','group');
    }

     public function permissions_route_name()
    {
        return $this->hasManyThrough(
            'Modules\User\Entities\Permissions',
            'Modules\User\Entities\RolesAndPermissions',
            'role_id', // Foreign key on the RolesAdminPermissions table...
            'id', // Foreign key on the permission table...
            'id', // Local key on the role table...
            'permission_id' // Local key on the RolesAdminPermissions table...
        )->select('user_permissions.id','user_permissions.route_name');
    }
}
