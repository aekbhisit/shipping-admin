<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminActivitiesErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'module',
        'function',
        'data_id' ,
        'data_request' ,
        'data_response' ,
        'created_at',
        'updated_at'
    ];
    protected $table = "admin_activities_log";
    protected $primaryKey = "id";
    
    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\AdminActivitiesLogFactory::new();
    }

   
}
