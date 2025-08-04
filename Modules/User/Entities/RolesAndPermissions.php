<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolesAndPermissions extends Model
{
    use HasFactory;

    protected $fillable = ['id','role_id','permission_id'];
    protected $table = "user_roles_permissions";
    protected $primaryKey = "id";
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\RolesAdminPermissionsFactory::new();
    }
}
