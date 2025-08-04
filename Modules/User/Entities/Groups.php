<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Kalnoy\Nestedset\NodeTrait;

class Groups extends Model
{
    use HasFactory;
    use NodeTrait;

    protected $fillable = ['id','name','description','default_role','status'];
    protected $table = "user_groups";
    protected $primaryKey = "id";
    
    protected static function newFactory()
    {
        return \Modules\User\Database\factories\GroupsFactory::new();
    }
}
